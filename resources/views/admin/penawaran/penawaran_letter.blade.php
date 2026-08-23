<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Penawaran {{ $penawaran->no_penawaran }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }

/* Reserve space at bottom for the fixed footer */
@page {
    margin: 0mm 0mm 18mm 0mm;
    size: A4 portrait;
}

/* ── TASK 1: CSS Global & Body — 10pt konsisten, hapus semua override individual ── */
body {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    color: #000;
    background: #fff;
    line-height: 1.4;
}

.page {
    padding: 12mm 18mm 4mm 18mm;
}

/* ── TASK 2: Header Logo — max-height 22mm, margin-bottom 10mm ── */
.header-table { width:100%; border-collapse:collapse; margin-bottom:5mm; }
.header-table td { vertical-align:middle; border:none; }
.logo-cell { width:52mm; }
.logo-cell img { max-height:22mm; max-width:52mm; }

/* ── TASK 3: Meta Surat — margin-bottom 5mm, lebar meta-lbl 16mm ── */
.meta { margin-bottom:5mm; }
.meta-date { margin-bottom:5mm; }
.meta-row { display:table; width:100%; margin-bottom:0.5mm; }
.meta-lbl { display:table-cell; width:16mm; }
.meta-sep { display:table-cell; width:4mm; }
.meta-val { display:table-cell; }

/* ── TASK 5: Tabel Kendaraan — hapus background header, padding lebih lapang ── */
.car-table { width:100%; border-collapse:collapse; margin:5mm 0; }
.car-table th {
    border:1px solid #000;
    padding:5px 6px;
    text-align:center;
    background:#fff;
    font-weight:bold;
}
.car-table td {
    border:1px solid #000;
    padding:5px 6px;
    vertical-align:middle;
}
.car-table td.c { text-align:center; }
.car-table td.r { text-align:right; }

/* ── TASK 4 & 6: Ketentuan — justify paragraf, sub-item indent 8mm ── */
.ketentuan { margin-bottom:2mm; line-height:1.3; }
.ketentuan p { margin-bottom:1mm; text-align:justify; }
.ketentuan p:first-child { text-align:left; }   /* "Dengan hormat," tetap kiri */
.ketentuan ul { margin:0.5mm 0 1mm 4mm; padding:0; list-style:none; }
.ketentuan ul li { margin-bottom:0.5mm; padding-left:10px; text-indent:-10px; }
.ketentuan ul li::before { content:"• "; }

/* ── TASK 8: Info Kontak — justify, font inherit 10pt ── */
.info-kontak { text-align:justify; margin:5mm 0; line-height:1.3; }

/* ── TASK 9: Signature ── */
.sign-table { width:100%; border-collapse:collapse; margin-top:0; }
.sign-table td { vertical-align:top; border:none; }
.sign-name { font-weight:bold; }
.sign-logo { max-height:11mm; max-width:28mm; opacity:0.45; margin:1.5mm 0; display:block; }
.sign-line { border-top:1px solid #000; width:60mm; margin-bottom:2mm; }

/* ── TASK 10: Footer — border-top, font-size, padding ── */
.page-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    padding: 8px 18mm 12px;
    background: #fff;
    border-top: none;
}
.footer-company {
    font-family: Arial, Helvetica, sans-serif;
    font-weight: 800;
    font-size: 8pt;
    color: #0d2a6e;
    letter-spacing: 0.3px;
}
.footer-addr {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 7pt;
    color: #1a3a8f;
    margin-top: 1px;
}
.footer-web {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #1a3a8f;
    font-weight: bold;
    text-decoration: underline;
    margin-top: 1px;
}
</style>
</head>
<body>
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $namaPerush = $setting->nama_perusahaan ?? 'PT. Anugerah Panca Yoga';
    $alamat     = $setting->alamat          ?? 'Jl. Dr. Saharjo No. 131 Jakarta 12860';
    $telepon    = $setting->telepon         ?? '021. 83792927, 021. 8354565';
    $fax        = $setting->fax             ?? '';
    $website    = $setting->website         ?? 'www.apy-rentacar.com';
    $tgl        = Carbon::parse($penawaran->tanggal_penawaran)->isoFormat('D MMMM YYYY');
@endphp

{{-- ── TASK 10: FOOTER (fixed, always at bottom, with border-top) ── --}}
<div class="page-footer">
    <div class="footer-company">{{ strtoupper($namaPerush) }}</div>
    <div class="footer-addr">
        Head Office : {{ $alamat }}&nbsp;&nbsp;
        {{ $telepon }}@if($fax), {{ $fax }}@endif
    </div>
    <div class="footer-web">{{ $website }}</div>
</div>

<div class="page">

{{-- ── TASK 2: HEADER: Logo lebih besar ── --}}
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

{{-- ── TASK 3: TANGGAL & META — margin lebih besar, lbl lebih rapat ── --}}
<div class="meta">
    <div class="meta-date">Jakarta, {{ $tgl }}</div>
    <div class="meta-row"><span class="meta-lbl">Kepada</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->kepada }}</span></div>
    <div class="meta-row"><span class="meta-lbl">No</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->no_penawaran }}</span></div>
    <div class="meta-row"><span class="meta-lbl">Perihal</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->perihal ?? 'Penawaran Harga Sewa Kendaraan' }}</span></div>
    <div class="meta-row"><span class="meta-lbl">U.P.</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->up }}</span></div>
</div>

{{-- ── TASK 4: SALAM — paragraf justify (kecuali "Dengan hormat,") ── --}}
<div class="ketentuan">
    <p style="text-align:left;">Dengan hormat,</p>
    <p>Berdasarkan kebutuhan perusahaan ibu/bapak akan kendaraan operasional, bersama ini kami hendak mengajukan penawaran dengan perincian sebagai berikut:</p>
</div>

{{-- ── TASK 5: TABEL KENDARAAN — header putih, kolom proporsional, format Rp. ── --}}
<table class="car-table">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th width="35%">Car Model</th>
            <th width="9%">Year</th>
            <th width="7%">Qty</th>
            <th width="28%">Rental Price/Unit (Rp)</th>
            <th width="16%">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @forelse($penawaran->items as $i => $item)
        <tr>
            <td class="c">{{ $i + 1 }}.</td>
            <td>{{ $item->kendaraan->merk ?? '-' }}</td>
            <td class="c">{{ $item->tahun_unit ?? ($item->kendaraan->tahun_pembuatan ?? '-') }}</td>
            <td class="c">{{ $item->qty ?? 1 }}</td>
            <td class="r">Rp. {{ number_format($item->price ?? 0, 0, ',', '.') }},-</td>
            <td class="c">{{ $item->durasi ?? '' }} {{ $item->satuan_durasi ?? '' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="c" style="color:#888; font-style:italic;">Belum ada item</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ── TASK 6: KETENTUAN — sub-item indent 8mm ── --}}
@php
    $defaultKetentuan = [
        ['teks' => 'Harga sewa termasuk PPN 11%, diluar BBM, Tol dan Parkir', 'sub' => []],
        ['teks' => 'TOP (Term of payment) min. 2 minggu setelah pengiriman kendaraan dan invoice diterima', 'sub' => []],
        ['teks' => 'Pembatalan kontrak di kenakan penalty sebesar 25% dari sisa nilai kontrak sewa kendaraan', 'sub' => []],
        ['teks' => 'Klaim own risk untuk kerusakan kendaraan sebesar Rp. 350.000,- / kejadian', 'sub' => []],
        ['teks' => 'Klaim own risk untuk kehilangan kendaraan sebesar 10% dari nilai pertanggungan', 'sub' => []],
        ['teks' => 'Harga penawaran ini berlaku selama 2 (dua) minggu sejak tanggal penawaran', 'sub' => []],
        ['teks' => 'Pengiriman Kendaraan 4 (Empat) minggu setelah PO / SPK diterima', 'sub' => []],
        ['teks' => 'Harga sudah termasuk :', 'sub' => [
            'Perawatan kendaraan (Maintenance, Sparepart, & Penggantian ban bisa dilakukan di tahun ke-3)',
            'Asuransi All Risk (TJH max. 10 jt)',
            'Kendaraan pengganti sementara',
            'Perpanjangan STNK dan KIR',
        ]],
    ];

    $ketentuanList = (!empty($penawaran->ketentuan) && is_array($penawaran->ketentuan))
        ? $penawaran->ketentuan
        : $defaultKetentuan;
@endphp

<div class="ketentuan">
    <p style="text-align:left;">Ketentuan:</p>
    <ul>
        @foreach($ketentuanList as $item)
            @php
                $teks = is_array($item) ? ($item['teks'] ?? '') : $item;
                $sub  = is_array($item) ? ($item['sub'] ?? []) : [];
            @endphp
            <li>
                {{ $teks }}
                @if(!empty($sub))
                    <div style="margin:1mm 0 1mm 8mm;">
                        @foreach($sub as $idx => $subItem)
                            <div style="margin-bottom:1mm;">{{ $idx + 1 }}. {{ $subItem }}</div>
                        @endforeach
                    </div>
                @endif
            </li>
        @endforeach
    </ul>

    {{-- ── TASK 7: Persyaratan — 3 baris terpisah tanpa bullet ── --}}
    <p style="text-align:left; margin-top:1mm;">Persyaratan untuk Perusahaan :</p>
    <p style="text-align:left; margin-bottom:0.3mm;">PO (Purchase Order) / SPK</p>
    <p style="text-align:left; margin-bottom:0.3mm;">Fc NIB, NPWP, SIUP, TDP &amp; Akta</p>
    <p style="text-align:left; margin-bottom:0.3mm;">Fc, KTP dan SIM penandatangan kontrak</p>
</div>

{{-- ── TASK 8: INFO KONTAK — justify ── --}}
<div class="info-kontak">
    Untuk keterangan lebih lanjut dapat menghubungi kantor kami di {{ $telepon }} atau mengunjungi website kami <span style="color:#1a56db;">{{ $website }}</span>
</div>

{{-- ── TASK 9: TANDA TANGAN — kanan rata kiri, underline nama staff, height 18mm ── --}}
<table class="sign-table">
    <tr>
        <td style="width:50%; vertical-align:top;">
            <p>Hormat kami,</p>
            <p class="sign-name">{{ $namaPerush }}</p>
            <div style="height:18mm;"></div>
            <div class="sign-line"></div>
            <p><u>{{ $penawaran->name_staff ?? '…………………………' }}</u></p>
            <p style="font-size:9.5pt;">({{ $penawaran->staff ?? 'Staff' }})</p>
        </td>
        <td style="width:50%; vertical-align:top; text-align:left; padding-left:20mm; padding-top:0;">
            <p>Disetujui Oleh,</p>
            <p class="sign-name">{{ $penawaran->kepada }}</p>
            <div style="height:18mm;"></div>
            <div class="sign-line"></div>
            <p>({{ $penawaran->up ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' }})</p>
        </td>
    </tr>
</table>

</div>{{-- end .page --}}
</body>
</html>
