<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            margin: 25px 30px 30px 30px;
            size: A4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ====== HEADER ====== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .logo-cell {
            width: 45%;
            vertical-align: bottom;
            padding-bottom: 0;
        }

        .logo-img {
            height: 56px;
        }

        .invoice-title-cell {
            width: 55%;
            vertical-align: bottom;
            text-align: center;
            padding-bottom: 4px;
        }

        .invoice-word {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #000;
            display: block;
            text-align: center;
        }

        .invoice-no {
            font-size: 10px;
            color: #000;
            display: block;
            text-align: center;
            margin-top: 2px;
        }

        /* ====== DIVIDER MERAH ====== */
        .divider-red {
            border: none;
            border-top: 3px solid #c0392b;
            margin: 5px 0 0 0;
            width: 45%;
            display: block;
        }

        /* ====== DIVIDER HITAM ====== */
        .divider-black {
            border: none;
            border-top: 1px solid #000;
            margin: 6px 0 8px 0;
        }

        /* ====== INFO CUSTOMER ====== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info-label {
            width: 110px;
            color: #000;
        }

        .info-colon {
            width: 10px;
            text-align: center;
        }

        .info-value {
            font-weight: bold;
        }

        /* ====== DETAIL TABLE ====== */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 10px;
        }

        .detail-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            background: #fff;
            font-weight: bold;
        }

        .detail-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            vertical-align: top;
        }

        .detail-table tr.no-border td {
            border: none !important;
            padding: 0;
            height: 10px;
        }

        .detail-table .col-periode  { width: 18%; text-align: center; }
        .detail-table .col-remaks   { width: 38%; text-align: left; }
        .detail-table .col-qty      { width: 7%;  text-align: center; }
        .detail-table .col-price    { width: 20%; text-align: center; }
        .detail-table .col-subtotal { width: 17%; text-align: right; }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .text-bold   { font-weight: bold; }

        /* ====== SUMMARY ====== */
        .summary-label {
            text-align: left;
            font-weight: normal;
            padding: 4px 6px;
            border: 1px solid #000;
        }

        .summary-value {
            text-align: right;
            padding: 4px 6px;
            border: 1px solid #000;
        }

        .grand-total-label {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 5px;
            border: 1px solid #000;
        }

        .grand-total-value {
            text-align: right;
            font-weight: bold;
            font-size: 11px;
            padding: 5px 6px;
            border: 1px solid #000;
        }

        .terbilang-row td {
            text-align: center;
            font-style: italic;
            font-size: 10px;
            padding: 5px;
            border: 1px solid #000;
        }

        /* ====== PAYMENT INFO ====== */
        .payment-section {
            margin-top: 14px;
            font-size: 10px;
            line-height: 1.7;
        }

        /* ====== SIGNATURE ====== */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .sig-left {
            width: 50%;
            vertical-align: top;
        }

        .sig-right {
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .sig-name {
            font-size: 10px;
            text-decoration: underline;
            font-weight: bold;
        }

        .sig-jabatan {
            font-size: 10px;
            font-style: italic;
        }

        .sig-img {
            height: 120px;
            display: block;
            margin-bottom: 2px;
        }

        /* ====== FOOTER ====== */
        .footer-box {
            margin-top: 28px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 7px 0 5px 0;
            text-align: center;
            font-size: 9px;
            line-height: 1.7;
        }

        .footer-company {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

@php
    $setting = $setting ?? null;

    // $logoSrc, $ttdStaffSrc, $ttdDirekturSrc dikirim dari controller sebagai base64 data URI
    // agar DomPDF bisa merender gambar dengan benar
    $logoSrc        = $logoSrc        ?? '';
    $ttdStaffSrc    = $ttdStaffSrc    ?? '';
    $ttdDirekturSrc = $ttdDirekturSrc ?? '';

    $tgl     = $invoice->invoice_date ?? now();
    $invDate = \Carbon\Carbon::parse($tgl);

    // Hitung sub-total dari remaks
    $subTotalItems = 0;
    foreach ($invoice->periodes as $periode) {
        foreach ($periode->remaks as $remak) {
            $subTotalItems += ($remak->qty ?? 1) * ($remak->price ?? 0);
        }
    }

    $ppnPct      = floatval($invoice->ppn ?? 0);
    $pphPct      = floatval($invoice->pph ?? 0);
    $ppnNom      = round($subTotalItems * $ppnPct / 100, 0);
    $pphNom      = round($subTotalItems * $pphPct / 100, 0);
    $subAfterPpn = $subTotalItems + $ppnNom;
    $grandTotal  = $subAfterPpn - $pphNom;

    // Fungsi terbilang
    if (!function_exists('penyebut_inv')) {
        function penyebut_inv($n) {
            $n = abs($n);
            $huruf = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
            if ($n < 12)             return ' '.$huruf[$n];
            elseif ($n < 20)         return penyebut_inv($n - 10).' Belas';
            elseif ($n < 100)        return penyebut_inv(intdiv($n, 10)).' Puluh'.penyebut_inv($n % 10);
            elseif ($n < 200)        return ' Seratus'.penyebut_inv($n - 100);
            elseif ($n < 1000)       return penyebut_inv(intdiv($n, 100)).' Ratus'.penyebut_inv($n % 100);
            elseif ($n < 2000)       return ' Seribu'.penyebut_inv($n - 1000);
            elseif ($n < 1000000)    return penyebut_inv(intdiv($n, 1000)).' Ribu'.penyebut_inv($n % 1000);
            elseif ($n < 1000000000) return penyebut_inv(intdiv($n, 1000000)).' Juta'.penyebut_inv($n % 1000000);
            elseif ($n < 1000000000000) return penyebut_inv(intdiv($n, 1000000000)).' Miliar'.penyebut_inv($n % 1000000000);
            return '';
        }
    }
    if (!function_exists('terbilang_inv')) {
        function terbilang_inv($n) { return '# '.trim(penyebut_inv($n)).' Rupiah #'; }
    }
@endphp

{{-- =================== HEADER =================== --}}
<table class="header-table">
    <tr>
        <td class="logo-cell">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="logo-img" alt="Logo">
            @endif
            {{-- Garis merah di bawah logo --}}
            <hr class="divider-red">
        </td>
        <td class="invoice-title-cell">
            <span class="invoice-word">I N V O I C E</span>
            <span class="invoice-no">No. {{ $invoice->invoice_no }}</span>
        </td>
    </tr>
</table>

<hr class="divider-black">

{{-- =================== INFO CUSTOMER =================== --}}
<table class="info-table">
    <tr>
        <td class="info-label">No. Order</td>
        <td class="info-colon">:</td>
        <td>{{ $invoice->order_no ?? '' }}</td>
    </tr>
    <tr>
        <td class="info-label">Nama Customer</td>
        <td class="info-colon">:</td>
        <td class="info-value">{{ $invoice->customer_name }}</td>
    </tr>
    <tr>
        <td class="info-label">Alamat</td>
        <td class="info-colon">:</td>
        <td>{{ $invoice->customer_address }}</td>
    </tr>
    <tr>
        <td class="info-label">Contact Person</td>
        <td class="info-colon">:</td>
        <td>{{ $invoice->contact_person }}</td>
    </tr>
    <tr>
        <td class="info-label">Telephone</td>
        <td class="info-colon">:</td>
        <td>{{ $invoice->telephone }}</td>
    </tr>
    <tr>
        <td class="info-label">Kendaraan</td>
        <td class="info-colon">:</td>
        <td>{{ $invoice->kendaraan ? $invoice->kendaraan->merk.' '.$invoice->kendaraan->nopol : '' }}</td>
    </tr>
</table>

{{-- =================== TABEL DETAIL =================== --}}
<table class="detail-table">
    <thead>
        <tr>
            <th class="col-periode">Periode</th>
            <th class="col-remaks">Remaks</th>
            <th class="col-qty">QTY</th>
            <th class="col-price">Car Rent/Day</th>
            <th class="col-subtotal">Sub Total</th>
        </tr>
    </thead>
    <tbody>

        {{-- Baris data periodes & remaks --}}
        @forelse($invoice->periodes as $periode)
            @php $remaks = $periode->remaks; @endphp
            @if($remaks->count() > 0)
                @foreach($remaks as $i => $remak)
                    <tr>
                        {{-- Kolom periode hanya di baris pertama remak --}}
                        @if($i === 0)
                            <td class="col-periode" rowspan="{{ $remaks->count() }}">
                                @if($periode->periode_awal && $periode->periode_akhir)
                                    {{ \Carbon\Carbon::parse($periode->periode_awal)->locale('id')->isoFormat('D MMMM YYYY') }}
                                    @if($periode->periode_awal != $periode->periode_akhir)
                                        <br>s/d<br>
                                        {{ \Carbon\Carbon::parse($periode->periode_akhir)->locale('id')->isoFormat('D MMMM YYYY') }}
                                    @endif
                                @elseif($periode->periode_awal)
                                    {{ \Carbon\Carbon::parse($periode->periode_awal)->locale('id')->isoFormat('D MMMM YYYY') }}
                                @endif
                            </td>
                        @endif
                        <td class="col-remaks">{{ $remak->remaks }}</td>
                        <td class="col-qty text-center">{{ $remak->qty ?? 1 }}</td>
                        <td class="col-price">
                            <table style="width:100%; border:none; border-collapse:collapse;">
                                <tr>
                                    <td style="border:none; padding:0; width:28px;">Rp</td>
                                    <td style="border:none; padding:0; text-align:right;">{{ number_format($remak->price ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="col-subtotal">
                            <table style="width:100%; border:none; border-collapse:collapse;">
                                <tr>
                                    <td style="border:none; padding:0; width:24px;">Rp</td>
                                    <td style="border:none; padding:0; text-align:right;">{{ number_format(($remak->qty ?? 1) * ($remak->price ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
            @else
                {{-- Periode tanpa remak --}}
                <tr>
                    <td class="col-periode">
                        @if($periode->periode_awal)
                            {{ \Carbon\Carbon::parse($periode->periode_awal)->locale('id')->isoFormat('D MMMM YYYY') }}
                        @endif
                    </td>
                    <td class="col-remaks"></td>
                    <td class="col-qty"></td>
                    <td class="col-price"></td>
                    <td class="col-subtotal"></td>
                </tr>
            @endif
        @empty
            {{-- Jika tidak ada data periodes --}}
            <tr>
                <td class="col-periode" style="height:60px;"></td>
                <td class="col-remaks"></td>
                <td class="col-qty"></td>
                <td class="col-price"></td>
                <td class="col-subtotal"></td>
            </tr>
        @endforelse

        {{-- Baris padding tanpa border sama sekali --}}
        <tr class="no-border">
            <td colspan="5"></td>
        </tr>

        {{-- ========== SUMMARY ========== --}}
        {{-- Total --}}
        <tr>
            <td colspan="2" style="border:none;"></td>
            <td colspan="2" class="summary-label">Total</td>
            <td class="summary-value">{{ number_format($subTotalItems, 0, ',', '.') }}</td>
        </tr>
        {{-- PPN --}}
        <tr>
            <td colspan="2" style="border:none;"></td>
            <td colspan="2" class="summary-label">PPN {{ $ppnPct > 0 ? number_format($ppnPct, 0).'%' : '' }}</td>
            <td class="summary-value">{{ $ppnNom > 0 ? number_format($ppnNom, 0, ',', '.') : '-' }}</td>
        </tr>
        {{-- Sub Total --}}
        <tr>
            <td colspan="2" style="border:none;"></td>
            <td colspan="2" class="summary-label">Sub Total</td>
            <td class="summary-value">{{ number_format($subAfterPpn, 0, ',', '.') }}</td>
        </tr>
        {{-- Pot PPh --}}
        <tr>
            <td colspan="2" style="border:none;"></td>
            <td colspan="2" class="summary-label">Pot PPh {{ $pphPct > 0 ? number_format($pphPct, 0).'%' : '' }}</td>
            <td class="summary-value">{{ $pphNom > 0 ? number_format($pphNom, 0, ',', '.') : '-' }}</td>
        </tr>
        {{-- Total Invoice --}}
        <tr>
            <td colspan="2" style="border:none;"></td>
            <td colspan="2" class="grand-total-label">Total Invoice</td>
            <td class="grand-total-value">{{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        {{-- Terbilang --}}
        <tr class="terbilang-row">
            <td colspan="5">{{ terbilang_inv($grandTotal) }}</td>
        </tr>

    </tbody>
</table>

{{-- =================== INFO PEMBAYARAN =================== --}}
<div class="payment-section">
    Please make payment by wire transfer to:<br>
    <strong>{{ $setting?->atas_nama_rekening ?? '' }}</strong><br>
    {{ $setting?->nama_bank ?? '' }}<br>
    IDR Acc. No. : {{ $setting?->nomor_rekening ?? '' }}
    <br><br>
    Bukti Transfer mohon di fax ke nomor {{ $setting?->telepon ?? '' }} / email ke {{ $setting?->email ?? '' }}<br>
    Apabila bukti pembayaran belum kami terima maka kami belum dapat memproses<br>
    pembayaran Invoice tersebut.
</div>

{{-- =================== TANDA TANGAN =================== --}}
<table class="signature-table">
    <tr>
        <td class="sig-left">
            {{-- Baris info: tanggal, perusahaan, prepared by --}}
            <div style="line-height: 1.7; font-size:10px;">
                {{ $invDate->locale('id')->isoFormat('D MMMM YYYY') }}<br>
                {{ $setting?->nama_perusahaan ?? 'PT. Anugerah Panca Yoga' }}<br>
                Prepared by,
            </div>

            {{-- Logo + TTD staff overlap: logo di belakang, TTD di depan --}}
            <div style="position:relative; display:block; height:90px; width:220px; margin-top:6px; margin-bottom:4px;">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" style="position:absolute; top:0; left:0; height:65px; opacity:0.6;" alt="Logo">
                @endif
                @if($ttdStaffSrc)
                    <img src="{{ $ttdStaffSrc }}" style="position:absolute; top:0; left:0; height:85px;" alt="TTD Staff">
                @endif
            </div>

            <div class="sig-name">{{ $invoice->name_staff }}</div>
            <div class="sig-jabatan">{{ $invoice->staff }}</div>
        </td>
        <td class="sig-right">
            <div style="line-height: 1.7; font-size:10px; padding-top: 38px;">
                Approved by,
            </div>

            {{-- TTD direktur saja, tanpa logo --}}
            <div style="margin-top:6px; margin-bottom:4px; text-align:right;">
                @if($ttdDirekturSrc)
                    <img src="{{ $ttdDirekturSrc }}" style="height:85px; display:inline-block;" alt="TTD Direktur">
                @else
                    <div style="height:85px;"></div>
                @endif
            </div>

            <div class="sig-name">{{ $invoice->name_direktur }}</div>
            <div class="sig-jabatan">{{ $invoice->direktur }}</div>
        </td>
    </tr>
</table>

{{-- =================== FOOTER PERUSAHAAN =================== --}}
<div class="footer-box">
    <div class="footer-company">{{ $setting?->nama_perusahaan ?? 'PT. ANUGERAH PANCA YOGA' }}</div>
    @if($setting?->alamat)
        Head Office : {{ $setting->alamat }}@if($setting?->telepon) Ph. {{ $setting->telepon }}@endif
        <br>
    @endif
    @if($setting?->website)
        {{ $setting->website }}
    @endif
</div>

</body>
</html>
