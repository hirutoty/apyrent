<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Penawaran {{ $penawaran->no_penawaran }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }

/* Reserve space at bottom for the fixed footer (~22mm) */
@page {
    margin: 0mm 0mm 22mm 0mm;
    size: A4 portrait;
}

body {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    color: #000;
    background: #fff;
    line-height: 1.35;
}

.page {
    padding: 12mm 18mm 4mm 18mm;
}

/* ── HEADER ── */
.header-table { width:100%; border-collapse:collapse; margin-bottom:4mm; }
.header-table td { vertical-align:middle; border:none; }
.logo-cell { width:48mm; }
.logo-cell img { max-height:16mm; max-width:46mm; }

/* ── META SURAT ── */
.meta { margin-bottom:3mm; font-size:10pt; }
.meta-date { margin-bottom:2mm; font-size:10pt; }
.meta-row { display:table; width:100%; margin-bottom:0.5mm; }
.meta-lbl { display:table-cell; width:22mm; font-size:10pt; }
.meta-sep { display:table-cell; width:4mm; font-size:10pt; }
.meta-val { display:table-cell; font-size:10pt; }

/* ── TABEL KENDARAAN ── */
.car-table { width:100%; border-collapse:collapse; margin:2.5mm 0; font-size:9.5pt; }
.car-table th {
    border:1px solid #000;
    padding:3px 5px;
    text-align:center;
    background:#f2f2f2;
    font-weight:bold;
    font-size:9.5pt;
}
.car-table td {
    border:1px solid #000;
    padding:3px 5px;
    vertical-align:middle;
    font-size:9.5pt;
}
.car-table td.c { text-align:center; }
.car-table td.r { text-align:right; }

/* ── KETENTUAN ── */
.ketentuan { font-size:9.5pt; margin-bottom:2mm; line-height:1.3; }
.ketentuan p { margin-bottom:1mm; }
.ketentuan ul { margin:0.5mm 0 1mm 4mm; padding:0; list-style:none; }
.ketentuan ul li { margin-bottom:0.5mm; padding-left:10px; text-indent:-10px; }
.ketentuan ul li::before { content:"• "; }
.ketentuan ol { margin:0.5mm 0 1mm 12mm; }
.ketentuan ol li { margin-bottom:0.5mm; }

/* ── INFO KONTAK ── */
.info-kontak { font-size:9.5pt; margin-bottom:3mm; line-height:1.3; }

/* ── SIGNATURE ── */
.sign-table { width:100%; border-collapse:collapse; margin-top:3mm; }
.sign-table td { vertical-align:top; border:none; font-size:10pt; }
.sign-name { font-weight:bold; font-size:10pt; }
.sign-logo { max-height:11mm; max-width:28mm; opacity:0.45; margin:1.5mm 0; display:block; }
.sign-line { border-top:1px solid #000; width:52mm; margin-bottom:2mm; }

/* ── FOOTER (fixed at very bottom, appears on every page) ── */
.page-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    border-top: 1.5px solid #000;
    padding: 2px 18mm 3px;
    background: #fff;
}
.footer-company { font-weight:bold; font-size:9.5pt; }
.footer-addr    { font-size:8pt; color:#222; }
.footer-web     { font-size:8pt; color:#1a56db; text-decoration:underline; }
</style>
</head>
<body>
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $namaPerush = $setting->nama_perusahaan ?? 'PT. Anugerah Panca Yoga';
    $alamat     = $setting->alamat          ?? 'Jl. Dr. Saharjo No.131, Jakarta 12860';
    $telepon    = $setting->telepon         ?? '021. 83792927, 021. 8354565';
    $fax        = $setting->fax             ?? '';
    $website    = $setting->website         ?? 'www.apy-rentacar.com';
    $tgl        = Carbon::parse($penawaran->tanggal_penawaran)->isoFormat('D MMMM YYYY');
@endphp

{{-- ── FOOTER (fixed, always at bottom) ── --}}
<div class="page-footer">
    <div class="footer-company">{{ strtoupper($namaPerush) }}</div>
    <div class="footer-addr">
        Head Office : {{ $alamat }}
        &nbsp;&nbsp; Ph. {{ $telepon }}
        @if($fax) &nbsp;&nbsp; Fax. {{ $fax }} @endif
    </div>
    <div class="footer-web">{{ $website }}</div>
</div>

<div class="page">

{{-- ── HEADER: Logo ── --}}
<table class="header-table">
    <tr>
        <td class="logo-cell">
            @if(!empty($logoSrc))
                <img src="{{ $logoSrc }}" alt="Logo">
            @else
                <span style="font-size:13pt; font-weight:bold;">{{ $namaPerush }}</span>
            @endif
        </td>
        <td></td>
    </tr>
</table>

{{-- ── TANGGAL & META ── --}}
<div class="meta">
    <div class="meta-date">Jakarta, {{ $tgl }}</div>
    <div class="meta-row"><span class="meta-lbl">Kepada</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->kepada }}</span></div>
    <div class="meta-row"><span class="meta-lbl">No</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->no_penawaran }}</span></div>
    <div class="meta-row"><span class="meta-lbl">Perihal</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->perihal ?? 'Penawaran Harga Sewa Kendaraan' }}</span></div>
    <div class="meta-row"><span class="meta-lbl">U.P.</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->up }}</span></div>
</div>

{{-- ── SALAM ── --}}
<div class="ketentuan">
    <p>Dengan hormat,</p>
    <p>Berdasarkan kebutuhan perusahaan ibu/bapak akan kendaraan operasional, bersama ini kami hendak mengajukan penawaran dengan perincian sebagai berikut:</p>
</div>

{{-- ── TABEL KENDARAAN ── --}}
<table class="car-table">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th width="30%">Car Model</th>
            <th width="10%">Year</th>
            <th width="8%">Qty</th>
            <th width="28%">Rental Price/Unit (Rp)</th>
            <th width="19%">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @forelse($penawaran->items as $i => $item)
        <tr>
            <td class="c">{{ $i + 1 }}.</td>
            <td>{{ $item->kendaraan->merk ?? '-' }}</td>
            <td class="c">{{ $item->tahun_unit ?? ($item->kendaraan->tahun_pembuatan ?? '-') }}</td>
            <td class="c">{{ $item->qty ?? 1 }}</td>
            <td class="r">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }},-</td>
            <td class="c">{{ $item->durasi ?? '' }} {{ $item->satuan_durasi ?? '' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="c" style="color:#888; font-style:italic;">Belum ada item</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ── KETENTUAN ── --}}
<div class="ketentuan">
    <p>Ketentuan:</p>
    <ul>
        <li>Harga sewa termasuk PPN 11%, diluar BBM, Tol dan Parkir</li>
        <li>TOP (Term of payment) min. 2 minggu setelah pengiriman kendaraan dan invoice diterima</li>
        <li>Pembatalan kontrak di kenakan penalty sebesar 25% dari sisa nilai kontrak sewa kendaraan</li>
        <li>Klaim own risk untuk kerusakan kendaraan sebesar Rp. 350.000,- / kejadian</li>
        <li>Klaim own risk untuk kehilangan kendaraan sebesar 10% dari nilai pertanggungan</li>
        <li>Harga penawaran ini berlaku selama 2 (dua) minggu sejak tanggal penawaran</li>
        <li>Pengiriman Kendaraan 4 (Empat) minggu setelah PO / SPK diterima</li>
        <li>Harga sudah termasuk :
            <ol>
                <li>Perawatan kendaraan (Maintenance, Sparepart, &amp; Penggantian ban bisa dilakukan di tahun ke-3)</li>
                <li>Asuransi All Risk (TJH max. 10 jt)</li>
                <li>Kendaraan pengganti sementara</li>
                <li>Perpanjangan STNK dan KIR</li>
            </ol>
        </li>
    </ul>
    <p>Persyaratan untuk Perusahaan :</p>
    <p>PO (Purchase Order) / SPK &nbsp;&bull;&nbsp; Fc NIB, NPWP, SIUP, TDP &amp; Akta &nbsp;&bull;&nbsp; Fc, KTP dan SIM penandatanganan kontrak</p>
</div>

<div class="info-kontak">
    Untuk keterangan lebih lanjut dapat menghubungi kantor kami di {{ $telepon }} atau mengunjungi website kami <span style="color:#1a56db;">{{ $website }}</span>
</div>

{{-- ── TANDA TANGAN ── --}}
<table class="sign-table">
    <tr>
        <td style="width:50%; vertical-align:top;">
            <p>Hormat kami,</p>
            <p class="sign-name">{{ $namaPerush }}</p>
            <div style="height:14mm;"></div>
            <div class="sign-line"></div>
            <p>{{ $penawaran->name_staff ?? '…………………………' }}</p>
            <p style="font-size:9.5pt;">({{ $penawaran->staff ?? 'Staff' }})</p>
        </td>
        <td style="width:50%; vertical-align:top; text-align:center; padding-top:0;">
            <p>Disetujui Oleh,</p>
            <p class="sign-name">{{ $penawaran->kepada }}</p>
            <div style="height:14mm;"></div>
            <div class="sign-line" style="margin-left:auto; margin-right:auto;"></div>
            <p>({{ $penawaran->up ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' }})</p>
        </td>
    </tr>
</table>

</div>{{-- end .page --}}
</body>
</html>
