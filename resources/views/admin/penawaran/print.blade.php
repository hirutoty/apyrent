<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Penawaran {{ $penawaran->no_penawaran }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html, body {
    height: 100%;
}

body {
    font-family: "Times New Roman", Times, serif;
    font-size: 10pt;
    color: #000;
    background: #e8e8e8;
    line-height: 1.4;
}

/* ── Toolbar screen-only ── */
.toolbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    background: #1e293b;
    color: #fff;
    padding: 10px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    z-index: 999;
    font-family: sans-serif;
    font-size: 13px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.toolbar a { color: #94a3b8; text-decoration: none; font-size: 12px; }
.toolbar a:hover { color: #fff; }
.btn-print {
    background: #3b82f6;
    color: #fff;
    border: none;
    padding: 7px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-family: sans-serif;
    margin-left: auto;
    font-weight: 600;
}
.btn-print:hover { background: #2563eb; }
body { padding-top: 52px; }

/* ── Wrapper kertas di layar ── */
.page-wrap {
    max-width: 210mm;
    margin: 20px auto 40px;
    background: #fff;
    box-shadow: 0 3px 16px rgba(0,0,0,0.2);
    /* flexbox untuk push footer ke bawah */
    display: flex;
    flex-direction: column;
    /* tinggi minimal 1 halaman A4 dikurangi margin @page */
    min-height: 257mm; /* 297mm - 12mm top - 28mm bottom */
    padding: 12mm 25mm 0 25mm;
}

/* Konten utama mengambil sisa ruang, mendorong footer ke bawah */
.print-content {
    flex: 1;
    overflow: hidden;
}

/* Spacer — hanya aktif di print via @media print */
.footer-spacer { display: none; }

/* ── HEADER ── */
.header-table { width:100%; border-collapse:collapse; margin-bottom:4mm; }
.header-table td { vertical-align:middle; border:none; }
.logo-cell { width:52mm; }
.logo-cell img { max-height:22mm; max-width:52mm; }

/* ── META SURAT ── */
.meta { margin-bottom:4mm; }
.meta-date { margin-bottom:3mm; }
.meta-row { display:table; width:100%; margin-bottom:0.3mm; }
.meta-lbl { display:table-cell; width:16mm; }
.meta-sep { display:table-cell; width:4mm; }
.meta-val { display:table-cell; }

/* ── SALAM / PARAGRAF ── */
.salam { margin-bottom:2mm; }
.salam p { margin-bottom:1mm; text-align:justify; }

/* ── TABEL KENDARAAN ── */
.car-table { width:100%; border-collapse:collapse; margin:3mm 0; }
.car-table th {
    border:1px solid #000;
    padding:2px 4px;
    text-align:center;
    background:#fff;
    font-weight:bold;
    font-size:10pt;
}
.car-table td {
    border:1px solid #000;
    padding:0 4px;
    vertical-align:middle;
    font-size:10pt;
    white-space:nowrap;
}
/* Kolom Year (3) dan Qty (4): tambah padding kiri-kanan */
.car-table td:nth-child(3),
.car-table th:nth-child(3),
.car-table td:nth-child(4),
.car-table th:nth-child(4) {
    padding-left: 12px;
    padding-right: 12px;
}
.car-table td.c { text-align:center; }
.car-table td.r { text-align:right; }

/* ── KETENTUAN ── */
.ketentuan { margin-bottom:2mm; line-height:1.4; font-size:10.5pt; }
.ketentuan p { margin-bottom:0.8mm; text-align:justify; font-size:10.5pt; }
.ketentuan ul { margin:0.3mm 0 0.8mm 4mm; padding:0; list-style:none; }
.ketentuan ul li { margin-bottom:0.3mm; padding-left:10px; text-indent:-10px; font-size:10.5pt; }
.ketentuan ul li::before { content:"• "; }

/* ── INFO KONTAK ── */
.info-kontak { text-align:justify; margin:3mm 0 0; line-height:1.3; }

/* ── TANDA TANGAN ── */
.sign-top-table { width:100%; border-collapse:collapse; margin-top:0; }
.sign-top-table td { vertical-align:top; border:none; }

.sign-bottom-table { width:100%; border-collapse:collapse; margin-top:2mm; }
.sign-bottom-table td { vertical-align:top; border:none; }
.sign-name { font-weight:bold; }

/* ── FOOTER (di dalam flow, selalu paling bawah karena flex) ── */
.page-footer {
    text-align: center;
    padding: 6px 0 10px;
    background: #fff;
    margin-top: auto;
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
    color: #112E81;
    font-weight: bold;
    text-decoration: underline;
    margin-top: 1px;
}

/* ── PRINT ── */
@media print {
    body {
        background: #fff;
        padding-top: 0;
    }
    .toolbar { display: none !important; }
    .page-wrap {
        max-width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
        min-height: 0;
        display: flex;
        flex-direction: column;
        /* Hanya paksa tinggi penuh jika ada konten yang perlu turun ke halaman 2 (≥5 item) */
        height: var(--page-height, auto);
    }
    /* Di print, print-content tidak boleh mengambil sisa ruang berlebih */
    .print-content {
        flex: 0 0 auto;
        overflow: visible;
    }
    /* Spacer mendorong footer ke bawah, bukan print-content */
    .footer-spacer {
        flex: 1;
    }
    @page {
        size: A4 portrait;
        margin: 12mm 25mm 14mm 25mm;
    }
    .sign-top-table {
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .sign-bottom-table {
        page-break-inside: avoid;
        break-inside: avoid;
    }
    /* Footer tetap di bawah, didorong oleh spacer */
    .page-footer {
        margin-top: 0;
        padding: 4px 0 6px;
        margin-bottom: 0;
    }
    /* Tanda tangan bawah di luar page-wrap: reset semua style page-wrap saat print */
    .sign-bottom-wrap {
        padding: 0 !important;
        margin: 0 !important;
        margin-top: 20mm !important;
        box-shadow: none !important;
        background: transparent !important;
        min-height: 0 !important;
        display: block !important;
    }
}
</style>
</head>
<body>

{{-- Toolbar screen-only --}}
<div class="toolbar">
    <span style="font-weight:600;">Penawaran</span>
    <span style="color:#94a3b8;">{{ $penawaran->no_penawaran }}</span>
    <a href="{{ route('penawaran.index') }}">← Kembali</a>
    <button class="btn-print" onclick="window.print()">🖨 Print / Save PDF (Ctrl+P)</button>
</div>

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $namaPerush = $setting->nama_perusahaan ?? 'PT. Anugerah Panca Yoga';
    $alamat     = $setting->alamat          ?? 'jl. Dr. Saharjo No. 131 Jakarta 12860';
    $telepon    = $setting->telepon         ?? '021. 83792927, 021. 8354565';
    $telepon2   = $setting->no_telepon_2    ?? '';
    $fax        = $setting->fax             ?? '';
    $website    = $setting->website         ?? 'www.apy-rentacar.com';
    $tgl        = Carbon::parse($penawaran->tanggal_penawaran)->isoFormat('D MMMM YYYY');

    $defaultKetentuan = [
        ['teks' => 'Harga sewa belum termasuk PPN 11%, diluar BBM, Tol, dan Parkir', 'sub' => []],
        ['teks' => 'TOP (Term of payment) min. 2 minggu setelah pengiriman kendaraan dan invoice diterima', 'sub' => []],
        ['teks' => 'Pembatalan kontrak di kenakan penalty sebesar 25% dari sisa nilai kontrak sewa kendaraan', 'sub' => []],
        ['teks' => 'Klaim own risk untuk kerusakan kendaraan sebesar Rp. 300.000,- / kejadian', 'sub' => []],
        ['teks' => 'Klaim own risk untuk kehilangan kendaraan sebesar 10% dari nilai pertanggungan', 'sub' => []],
        ['teks' => 'Harga penawaran ini berlaku selama 2 (dua) minggu sejak tanggal penawaran', 'sub' => []],
        ['teks' => 'Pengiriman Kendaraan min. 3 (Tiga) minggu setelah PO / SPK diterima', 'sub' => []],
        ['teks' => 'Biaya pengiriman kendaraan dan survey sebesar Rp. 150.000,-', 'sub' => []],
        ['teks' => 'Harga sudah termasuk :', 'sub' => [
            'Perawatan kendaraan (Maintenance & Sparepart)',
            'Asuransi All Risk (TJH max. 10 jt)',
            'Kendaraan pengganti sementara',
            'Perpanjangan STNK',
        ]],
    ];

    $ketentuanList = (!empty($penawaran->ketentuan) && is_array($penawaran->ketentuan))
        ? $penawaran->ketentuan
        : $defaultKetentuan;

    $itemCount = $penawaran->items->count();
@endphp

<div class="page-wrap" style="--page-height: {{ $itemCount >= 5 ? '273mm' : 'auto' }};">

    {{-- Semua konten utama dalam print-content (flex: 1) --}}
    <div class="print-content">

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

        {{-- ── META SURAT ── --}}
        <div class="meta">
            <div class="meta-date">Jakarta, {{ $tgl }}</div>
            <div class="meta-row"><span class="meta-lbl">Kepada</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->kepada }}</span></div>
            <div class="meta-row"><span class="meta-lbl">No</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->no_penawaran }}</span></div>
            <div class="meta-row"><span class="meta-lbl">Perihal</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->perihal ?? 'Penawaran Harga Sewa Kendaraan' }}</span></div>
            <div class="meta-row"><span class="meta-lbl">UP.</span><span class="meta-sep">:</span><span class="meta-val">{{ $penawaran->up }}</span></div>
        </div>

        {{-- ── SALAM ── --}}
        <div class="salam">
            <p style="text-align:left;">Dengan hormat,</p>
            <p>Berdasarkan kebutuhan perusahaan bapak/ibu akan kendaraan operasional, bersama ini kami hendak mengajukan penawaran dengan perincian sebagai berikut :</p>
        </div>

        {{-- ── TABEL KENDARAAN ── --}}
        <table class="car-table">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="35%" style="text-align:left;">Car Model</th>
                    <th width="9%">Year</th>
                    <th width="7%">Qty</th>
                    <th width="28%">Rental Price/Month (Rp)</th>
                    <th width="16%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penawaran->items as $i => $item)
                <tr>
                    <td class="c">{{ $i + 1 }}.</td>
                    <td>{{ $item->kendaraan->merk ?? '-' }}</td>
                    <td class="c">{{ $item->tahun_unit ?? ($item->kendaraan->tahun_pembuatan ?? '-') }}</td>
                    <td class="c">{{ $item->qty ?? 1 }} Unit</td>
                    <td class="c">Rp. {{ number_format($item->price ?? 0, 0, ',', '.') }},-</td>
                    <td class="c">{{ $item->durasi ?? '' }} {{ $item->satuan_durasi ?? '' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="c" style="color:#888; font-style:italic;">Belum ada item</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── KETENTUAN ── --}}
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
                            <div style="margin:0.5mm 0 0.5mm 8mm;">
                                @foreach($sub as $idx => $subItem)
                                    <div style="margin-bottom:0.3mm;">{{ $idx + 1 }}. {{ $subItem }}</div>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <p style="text-align:left; margin-top:0.8mm;">Persyaratan untuk Perusahaan :</p>
            <p style="text-align:left; margin-bottom:0.2mm;">PO (Purchase Order) / SPK</p>
            <p style="text-align:left; margin-bottom:0.2mm;">Fc NIB, NPWP, SIUP, TDP &amp; Akta</p>
            <p style="text-align:left; margin-bottom:0.2mm;">Fc, KTP dan SIM penandatangan kontrak</p>
        </div>

        {{-- ── INFO KONTAK ── --}}
        <div class="info-kontak">
            Untuk keterangan lebih lanjut dapat menghubungi kantor kami di {{ $telepon }}@if(!empty($telepon2)) atau {{ $telepon2 }}@endif atau mengunjungi website kami <span style="color:#1C177E;font-weight:bold;text-decoration:underline;">{{ $website }}</span>
        </div>

        {{-- ── TANDA TANGAN ATAS: label + nama perusahaan (selalu halaman 1, tanpa foto) ── --}}
        @php
            $hasTtd = !empty($penawaran->ttd_image) && file_exists(public_path($penawaran->ttd_image));
            if ($hasTtd) {
                $ttdPath = public_path($penawaran->ttd_image);
                $ttdMime = mime_content_type($ttdPath) ?: 'image/png';
                $ttdSrc  = 'data:' . $ttdMime . ';base64,' . base64_encode(file_get_contents($ttdPath));
            }
        @endphp

    </div>{{-- /print-content --}}

    {{-- sign-top, foto, nama, jabatan semua di luar print-content agar rapat --}}
    <table class="sign-top-table" style="page-break-inside:avoid; break-inside:avoid; margin-top:5mm;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:5mm;">
                <p>Hormat kami,</p>
                <p class="sign-name">{{ $namaPerush }}</p>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:35mm; text-align:left;">
                <p>Disetujui Oleh,</p>
                <p class="sign-name">{{ $penawaran->kepada }}</p>
            </td>
        </tr>
    </table>

    {{-- ── FOTO TTD: di luar print-content tapi dalam page-wrap agar tidak geser footer
         Data 1-8: foto di halaman 1 (di antara nama perusahaan dan footer)
         Data ≥9 : foto turun ke halaman 2 (ada di sign-bottom-wrap di luar page-wrap)
    ── --}}
    @if($hasTtd && $itemCount <= 8)
    <div style="width:100%; padding-top:0; margin-top:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:5mm; border:none;">
                    <img src="{{ $ttdSrc }}" alt="TTD" style="max-height:12mm; max-width:45mm; display:block;">
                </td>
                <td style="width:50%; vertical-align:top; padding-left:35mm; border:none;">
                    <div style="height:12mm;"></div>
                </td>
            </tr>
        </table>
    </div>
    @elseif($itemCount <= 8)
    <div style="width:100%; margin-top:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; border:none;"><div style="height:12mm;"></div></td>
                <td style="width:50%; padding-left:35mm; border:none;"><div style="height:12mm;"></div></td>
            </tr>
        </table>
    </div>
    @endif

    {{-- ── NAMA + JABATAN dalam page-wrap (setelah foto TTD, sebelum footer) ── --}}
    @if($itemCount <= 5)
    <table style="width:100%; border-collapse:collapse; margin-top:4mm;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:5mm; border:none;">
                <p><u>{{ $penawaran->name_staff ?? '…………………………' }}</u></p>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:35mm; text-align:left; border:none;">
                <p>{{ $penawaran->up ?? '' }}</p>
            </td>
        </tr>
    </table>
    @endif

    @if($itemCount <= 4)
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:5mm; border:none;">
                <p>({{ $penawaran->staff ?? 'Staff' }})</p>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:35mm; border:none;"></td>
        </tr>
    </table>
    @endif

    {{-- ── FOOTER halaman 1: didorong ke bawah oleh spacer ── --}}
    <div class="footer-spacer"></div>
    <div class="page-footer">
        <div class="footer-company">{{ strtoupper($namaPerush) }}</div>
        <div class="footer-addr">
            Head Office : {{ $alamat }}&nbsp;&nbsp;
            {{ $telepon }}@if($fax), {{ $fax }}@endif
        </div>
        <div class="footer-web">{{ $website }}</div>
    </div>

</div>{{-- /page-wrap --}}

{{-- Spacer antara halaman 1 dan halaman 2 --}}
@if($itemCount >= 5)
<div style="height: 4mm;"></div>
@endif

{{--
    Di luar page-wrap (mengalir setelah halaman 1):
    - Data ≤4  : tidak ada apa-apa di sini (semua sudah di halaman 1)
    - Data 5   : hanya jabatan yang turun
    - Data 6-8 : nama + jabatan turun (foto sudah di halaman 1)
    - Data ≥9  : foto + nama + jabatan turun ke halaman 2
--}}
@if($itemCount >= 5)
{{-- ── HALAMAN 2: Wrapper kertas (screen only) ── --}}
<div class="sign-bottom-wrap page-wrap" style="margin-top: 6px; min-height: auto; padding: 12mm 25mm 12mm 25mm;">

    @if($itemCount >= 6)
    {{-- Nama turun ke halaman 2 --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:0;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:5mm; border:none;">
                {{-- Untuk data ≥9: foto ikut turun ke halaman 2 --}}
                @if($hasTtd && $itemCount >= 9)
                    <img src="{{ $ttdSrc }}" alt="TTD" style="max-height:18mm; max-width:50mm; display:block; margin-bottom:2mm;">
                @elseif($itemCount >= 9)
                    <div style="height:18mm;"></div>
                @endif
                <p><u>{{ $penawaran->name_staff ?? '…………………………' }}</u></p>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:35mm; text-align:left; border:none;">
                @if($itemCount >= 9)
                    <div style="height:18mm;"></div>
                @endif
                <p>{{ $penawaran->up ?? '' }}</p>
            </td>
        </tr>
    </table>
    @endif

    {{-- Jabatan (hanya untuk data ≥5, karena ≤4 sudah di halaman 1) --}}
    @if($itemCount >= 5)
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:5mm; border:none;">
                <p>({{ $penawaran->staff ?? 'Staff' }})</p>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:35mm; border:none;"></td>
        </tr>
    </table>
    @endif

</div>
@endif{{-- /sign-bottom-wrap: hanya untuk ≥5 item --}}

</body>
</html>
