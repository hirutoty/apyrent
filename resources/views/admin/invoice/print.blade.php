<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .border {
            border: 1px solid #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .logo {
            height: 55px;
        }

        .invoice-title {
            font-size: 24px;
            letter-spacing: 4px;
        }

        td,
        th {
            padding: 4px;
        }

        .signature {
            width: 220px;
            text-align: center;
            float: right;
        }

        .signature img {
            height: 70px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #1e40af;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .detail-table th {
            background: #1e40af;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border: 1px solid #dbeafe;
        }

        .detail-table td {
            border: 1px solid #dbeafe;
            padding: 8px;
        }

        .detail-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .summary-row {
            background: #eff6ff;
        }

        .total-row {
            background: #1e40af;
            color: #fff;
            font-weight: bold;
        }

        .terbilang-box {
            margin-top: 12px;
            padding: 10px;
            border-left: 4px solid #1e40af;
            background: #eff6ff;
            font-size: 10px;
        }

        .customer-box {
            border: 1px solid #dbeafe;
            background: #f8fafc;
            padding: 8px;
        }

        .bank-box {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #dbeafe;
            background: #f8fafc;
        }

        .payment-note {
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #dbeafe;
            background: #fff7ed;
            font-size: 10px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    @php
        $setting = $setting ?? null;

        $subTotal = $invoice->subtotal;
        $ppn = $invoice->ppn;
        $grandTotal = $invoice->total;

        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $invoiceDate = \Carbon\Carbon::parse($invoice->created_at ?? now());

        $invoiceNo = $invoice->id . '/INV/' . $bulanRomawi[$invoiceDate->month] . '/' . $invoiceDate->year;

        $mulai = \Carbon\Carbon::parse($invoice->start_date);
        $selesai = \Carbon\Carbon::parse($invoice->end_date);
        $durasiBulan = $mulai->diffInMonths($selesai) + 1;
    @endphp

    {{-- HEADER --}}
    <table>
        <tr>
            <td width="40%">
                @if ($setting?->logo)
                    <img src="{{ public_path($setting->logo) }}" class="logo">
                @endif
            </td>

            <td width="60%">
                <div style="
    font-size:30px;
    font-weight:bold;
    color:#1e40af;
    letter-spacing:3px;
">
                    INVOICE
                </div>
                <div class="bold">No. {{ $invoiceNo }}</div>
            </td>
        </tr>
    </table>

    <br>

    {{-- CUSTOMER --}}
    <table class="customer-box">
        <tr>
            <td width="18%">Nama Customer</td>
            <td width="2%">:</td>
            <td>{{ $invoice->customer_name }}</td>
        </tr>

        <tr>
    <tr>
    <td style="width: 120px;">Alamat</td>
    <td style="width: 10px;">:</td> 
    <td style="max-width: 200px; word-break: break-word; white-space: normal;">
        {{ $invoice->address }}
    </td>
</tr>
</tr>

        <tr>
            <td>Contact Person</td>
            <td>:</td>
            <td>{{ $invoice->contact_person }}</td>
        </tr>

        <tr>
            <td>Telepon</td>
            <td>:</td>
            <td>{{ $invoice->phone }}</td>
        </tr>
    </table>

    <br>

    {{-- DETAIL --}}
    <table class="detail-table">

        <thead>
            <tr>
                <th width="18%">Periode</th>
                <th width="25%">Keterangan</th>
                <th width="10%">Qty</th>
                <th width="12%">Durasi</th>
                <th width="15%">Harga Satuan</th>
                <th width="20%">Jumlah</th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td class="center">
                    {{ $mulai->format('d M Y') }}
                    <br>s/d<br>
                    {{ $selesai->format('d M Y') }}
                </td>

                <td>
                    <strong>{{ $invoice->remarks ?? 'Service' }}</strong>
                </td>

                <td class="center">
                    {{ $invoice->qty }}
                </td>

                <td class="center">
                    {{ $durasiBulan }} Bulan
                </td>

                <td class="right">
                    Rp {{ number_format($invoice->per_month, 0, ',', '.') }}
                    <br>
                    <small>/Bulan</small>
                </td>

                <td class="right">
                    Rp {{ number_format($subTotal, 0, ',', '.') }}
                </td>
            </tr>

            
            <tr class="summary-row">
                <td colspan="5" class="right">
                    <strong>PPN (11%)</strong>
                </td>
                <td class="right">
                    Rp {{ number_format($ppn, 0, ',', '.') }}
                </td>
            </tr>

            <tr class="total-row">
                <td colspan="5" class="right">
                    SubTotal
                </td>
                <td class="right">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </td>
            </tr>

        </tbody>

    </table>

    @php
        function penyebut($nilai)
        {
            $nilai = abs($nilai);
            $huruf = [
                '',
                'Satu',
                'Dua',
                'Tiga',
                'Empat',
                'Lima',
                'Enam',
                'Tujuh',
                'Delapan',
                'Sembilan',
                'Sepuluh',
                'Sebelas',
            ];

            if ($nilai < 12) {
                return ' ' . $huruf[$nilai];
            } elseif ($nilai < 20) {
                return penyebut($nilai - 10) . ' Belas';
            } elseif ($nilai < 100) {
                return penyebut($nilai / 10) . ' Puluh' . penyebut($nilai % 10);
            } elseif ($nilai < 200) {
                return ' Seratus' . penyebut($nilai - 100);
            } elseif ($nilai < 1000) {
                return penyebut($nilai / 100) . ' Ratus' . penyebut($nilai % 100);
            } elseif ($nilai < 2000) {
                return ' Seribu' . penyebut($nilai - 1000);
            } elseif ($nilai < 1000000) {
                return penyebut($nilai / 1000) . ' Ribu' . penyebut($nilai % 1000);
            } elseif ($nilai < 1000000000) {
                return penyebut($nilai / 1000000) . ' Juta' . penyebut($nilai % 1000000);
            } elseif ($nilai < 1000000000000) {
                return penyebut($nilai / 1000000000) . ' Miliar' . penyebut($nilai % 1000000000);
            }

            return '';
        }

        function terbilang($nilai)
        {
            return trim(penyebut($nilai)) . ' Rupiah';
        }
    @endphp

    <div class="terbilang-box">
        <strong>Terbilang :</strong><br>
        <em>{{ terbilang($grandTotal) }}</em>
    </div>

    {{-- SIGNATURE --}}
@if ($invoice->signature)
    <div class="signature" style="text-align:center; float:right; width:220px;">

        <p style="margin-bottom:5px;">Hormat Kami</p>

        @if (Str::startsWith($invoice->signature, 'data:image'))
            <img src="{{ $invoice->signature }}" style="height:70px;">
        @else
            <img src="{{ public_path($invoice->signature) }}" style="height:70px;">
        @endif

        {{-- NAMA & JABATAN --}}
        <div style="margin-top:10px; font-size:11px;">
            <strong>{{ $setting->ttd_nama ?? 'Hirutoty' }}</strong>
        </div>

        <div style="font-size:10px; color:#555;">
            {{ $setting->ttd_jabatan ?? 'Admin' }}
        </div>

    </div>

    <div style="clear:both;"></div>
@endif

    <div class="bank-box">
        <strong>INFORMASI PEMBAYARAN</strong>
        <br><br>

        Nama Bank :
        <strong>{{ $setting->nama_bank ?? '-' }}</strong>
        <br>

        Nomor Rekening :
        <strong>{{ $setting->nomor_rekening ?? '-' }}</strong>
        <br>

        Atas Nama :
        <strong>{{ $setting->atas_nama_rekening ?? '-' }}</strong>
    </div>

    <div class="payment-note">
        <strong>Catatan Pembayaran :</strong><br>
        Bukti transfer mohon di fax ke nomor {{ $setting->telepon ?? '-' }} atau email ke
        <strong>{{ $setting->email ?? '-' }}</strong>
        <br>
        Apabila bukti pembayaran belum kami terima maka kami belum dapat memproses
        pembayaran invoice tersebut.
    </div>

    <div class="footer">
        <strong>{{ $setting->nama_perusahaan ?? '-' }}</strong>
        <br>
        Telp : {{ $setting->telepon ?? '-' }}
        |
        Email : {{ $setting->email ?? '-' }}
        @if (!empty($setting?->website))
            |
            {{ $setting->website }}
        @endif
        <br>

        {{ $setting->alamat ?? '-' }}
    </div>

</body>

</html>