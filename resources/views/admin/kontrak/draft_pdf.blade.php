<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Perjanjian Sewa Menyewa - {{ $kontrak->no_kontrak }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    color: #000;
    background: #fff;
    line-height: 1.45;
}
.page {
    padding: 22mm 20mm 18mm 20mm;
    page-break-after: always;
}
.page:last-child { page-break-after: avoid; }

/* ── JUDUL ── */
.doc-title { text-align: center; margin-bottom: 18px; }
.doc-title .t1 { font-size: 13pt; font-weight: bold; text-transform: uppercase; display: block; }
.doc-title .t2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; display: block; margin-top: 1px; }
.doc-title .t3 { font-size: 11pt; font-weight: normal; display: block; margin-top: 3px; }

/* ── DUA KOLOM ── */
.tc { width: 100%; border-collapse: collapse; table-layout: fixed; }
.tc td {
    width: 50%;
    vertical-align: top;
    text-align: justify;
    hyphens: auto;
    padding: 0 10px 0 0;
    font-size: 11pt;
    line-height: 1.45;
}
.tc td.r { padding: 0 0 0 10px; }

/* ── JUDUL PASAL ── */
.ptitle {
    text-align: center;
    font-weight: bold;
    font-size: 11pt;
    text-transform: uppercase;
    padding: 8px 0 3px;
    line-height: 1.35;
}

/* ── PARAGRAF ── */
p { text-align: justify; margin-bottom: 5px; font-size: 11pt; }

/* ── NUMBERED LIST ── */
.lst { list-style: none; padding: 0; margin: 4px 0 6px 0; }
.lst > li {
    display: table;
    width: 100%;
    margin-bottom: 5px;
    text-align: justify;
    font-size: 11pt;
    line-height: 1.45;
}
.lst > li > .nb { display: table-cell; width: 22px; vertical-align: top; white-space: nowrap; }
.lst > li > .tx { display: table-cell; vertical-align: top; text-align: justify; }

/* ── ALPHA SUB-LIST ── */
.alst { list-style: none; padding: 0; margin: 4px 0 4px 0; }
.alst > li { display: table; width: 100%; margin-bottom: 4px; text-align: justify; font-size: 11pt; line-height: 1.45; }
.alst > li > .nb { display: table-cell; width: 18px; vertical-align: top; white-space: nowrap; }
.alst > li > .tx { display: table-cell; vertical-align: top; text-align: justify; }

/* ── SPACERS ── */
.s4  { height: 4px;  display: block; }
.s8  { height: 8px;  display: block; }
.s12 { height: 12px; display: block; }
.s18 { height: 18px; display: block; }
.s30 { height: 30px; display: block; }
.s55 { height: 55px; display: block; }

/* ── NOMOR HALAMAN ── */
.pgn {
    position: fixed;
    bottom: 10mm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 11pt;
}
.pgn:after {
    content: counter(page);
}

/* ── GARIS TTD ── */
.sline { border-top: 1px solid #000; width: 210px; margin-top: 40px; margin-bottom: 3px; }

/* ── PRE-WRAP untuk teks multi-baris ── */
.pre { white-space: pre-wrap; text-align: justify; }

@page { margin: 0mm; }
</style>
</head>
<body>
@php
    use Carbon\Carbon;
    use App\Helpers\KontrakHelper;
    Carbon::setLocale('id');

    $tgl = Carbon::parse($kontrak->tanggal_kontrak);

    $hariId = $tgl->isoFormat('dddd');
    $tglId  = $tgl->isoFormat('D MMMM YYYY');
    $hariEn = $tgl->locale('en')->isoFormat('dddd');
    $tglEn  = $tgl->locale('en')->isoFormat('D MMMM YYYY');
    Carbon::setLocale('id');

    $kendaraanList = ($kontrak->penawaran && $kontrak->penawaran->items)
        ? $kontrak->penawaran->items->filter(fn($i) => $i->kendaraan)
        : collect();

    $dV = $kontrak->durasi_value  ?? '';
    $dS = $kontrak->durasi_satuan ? ucfirst($kontrak->durasi_satuan) : '';

    $mulaiId   = $kontrak->tanggal_kontrak  ? Carbon::parse($kontrak->tanggal_kontrak)->isoFormat('D MMMM YYYY')  : '..........';
    $selesaiId = $kontrak->tanggal_selesai  ? Carbon::parse($kontrak->tanggal_selesai)->isoFormat('D MMMM YYYY')  : '..........';
    $mulaiEn   = $kontrak->tanggal_kontrak  ? Carbon::parse($kontrak->tanggal_kontrak)->locale('en')->isoFormat('D MMMM YYYY') : '..........';
    $selesaiEn = $kontrak->tanggal_selesai  ? Carbon::parse($kontrak->tanggal_selesai)->locale('en')->isoFormat('D MMMM YYYY') : '..........';

    $namaPerush  = $setting->nama_perusahaan    ?? 'PT. Anugerah Panca Yoga';
    $alamatPerush= $setting->alamat             ?? 'Jl. Catur No. 16, Menteng Dalam, Tebet, Jakarta Selatan 12870';
    $telpPerush  = $setting->telepon            ?? '021 - 83792927';
    $faxPerush   = $setting->fax               ?? '021 - 8354565';
    $namaBank    = $setting->nama_bank          ?? 'BCA';
    $noRek       = $setting->nomor_rekening     ?? '272-1420-878';
    $atasNama    = $setting->atas_nama_rekening ?? $kontrak->pihak_pertama;
    $nilaiKontrak= $kontrak->penawaran ? $kontrak->penawaran->total : 0;

    $ttdId = $tgl->isoFormat('D MMMM YYYY');
    $ttdEn = $tgl->locale('en')->isoFormat('D MMMM YYYY');
    Carbon::setLocale('id');

    $pihak1Jabatan = 'General Manager';

    // KTP Pihak Pertama diambil dari kolom contact_pertama
    $pihak1KTP = $kontrak->contact_pertama ?? '…………………………………';

    // Data Pihak Kedua
    $namaP2 = $kontrak->pihak_kedua
               ?: ($kontrak->penawaran->customer_name ?? null)
               ?: ($kontrak->penawaran->kepada ?? '…………………………………');

    $alamatP2 = ($kontrak->alamat_kedua ?: null)
                ?? ($kontrak->penawaran->alamat ?? null);

    $noktp2 = ($kontrak->no_ktp_kedua ?: null)
               ?? ($kontrak->penawaran->no_ktp ?? null);

    if (!$alamatP2 || !$noktp2) {
        $pelangganP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->pihak_kedua ?? '')
                        ->first();
        if (!$pelangganP2 && !empty($kontrak->penawaran->customer_name)) {
            $pelangganP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->penawaran->customer_name)->first();
        }
        if (!$pelangganP2 && !empty($kontrak->penawaran->kepada)) {
            $pelangganP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->penawaran->kepada)->first();
        }
        $alamatP2 = $alamatP2 ?? $pelangganP2?->alamat ?? '…………………………………………………………………………';
        $noktp2   = $noktp2   ?? $pelangganP2?->no_ktp ?? '……………………………………';
    }

    // PPN & PPH dari setting
    $ppn = $setting->ppn_default ?? 11;
    $pph = $setting->pph_default ?? 2;

    // ── Pasal ketentuan dari DB atau fallback default ──────────────────────
    $pasalKetentuan = $kontrak->pasal_ketentuan ?: KontrakHelper::defaultPasalKetentuan();

    // Map placeholder → nilai aktual
    $durasiStr   = ($dV && $dS) ? "$dV $dS" : '…………';
    $placeholders = [
        '{DURASI}'              => $durasiStr,
        '{TANGGAL_MULAI}'       => $mulaiId,
        '{TANGGAL_SELESAI}'     => $selesaiId,
        '{TANGGAL_MULAI_EN}'    => $mulaiEn,
        '{TANGGAL_SELESAI_EN}'  => $selesaiEn,
        '{PPN}'                 => $ppn,
        '{PPH}'                 => $pph,
        '{NAMA_BANK}'           => $namaBank,
        '{NO_REKENING}'         => $noRek,
        '{ATAS_NAMA}'           => $atasNama,
        '{NAMA_PERUSAHAAN}'     => $namaPerush,
        '{ALAMAT_PERUSAHAAN}'   => $alamatPerush,
        '{TELEPON_PERUSAHAAN}'  => $telpPerush,
        '{FAX_PERUSAHAAN}'      => $faxPerush,
        '{NAMA_PIHAK_KEDUA}'    => $namaP2,
        '{ALAMAT_PIHAK_KEDUA}'  => $alamatP2,
        '{KONTAK_PIHAK_KEDUA}'  => $kontrak->contact_kedua ?? '…..........................',
    ];

    $replaceTxt = function(string $txt) use ($placeholders): string {
        return str_replace(array_keys($placeholders), array_values($placeholders), $txt);
    };
@endphp

{{-- ════════════════════ HALAMAN 1 ════════════════════ --}}
<div class="page">

<div class="doc-title">
    <span class="t1">PERJANJIAN SEWA MENYEWA KENDARAAN</span>
    <span class="t2">CAR RENTAL AGREEMENT</span>
    <span class="t3">NO : {{ $kontrak->no_kontrak }}</span>
</div>

<table class="tc">
<tr>
    <td>
        <p>Pada hari ini, {{ $hariId }}, tanggal {{ $tglId }},
        kami yang bertanda tangan dibawah ini, masing-masing:</p>
    </td>
    <td class="r">
        <p>On this day, {{ $hariEn }}, {{ $tglEn }},
        we the undersigned, respectively:</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td>
        <p><strong>1. {{ $namaPerush }},</strong> suatu perseroan terbatas yang memiliki
        kantor terdaftar di {{ $alamatPerush }}, dalam hal ini diwakili oleh
        {{ $kontrak->pihak_pertama }},
        jabatan {{ $pihak1Jabatan }},
        pemegang KTP No. {{ $pihak1KTP }},
        selanjutnya disebut "Pihak Pertama"; dan</p>
    </td>
    <td class="r">
        <p><strong>1. {{ $namaPerush }},</strong> a limited liability company, having its
        registered office at {{ $alamatPerush }}, in this matter represented by
        {{ $kontrak->pihak_pertama }},
        acting as {{ $pihak1Jabatan }}, a holder of identity card no. {{ $pihak1KTP }},
        hereinafter referred to as the "First Party"; and</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td>
        <p>2. <strong>{{ $namaP2 }},</strong>
        yang beralamat di {{ $alamatP2 }},
        pemegang KTP No. {{ $noktp2 }}, dalam hal ini
        Pihak Kedua {{ $kontrak->pihak_kedua ?: '………………………….' }},
        selanjutnya disebut sebagai "Pihak Kedua"</p>
    </td>
    <td class="r">
        <p><strong>2 . {{ $namaP2 }}.,</strong>
        domiciled at {{ $alamatP2 }},
        holder of identity card no. {{ $noktp2 }},
        Second Party {{ $kontrak->pihak_kedua ?: '………………………….' }},
        hereinafter referred to as the "Second Party".</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td>
        <p>Para Pihak sepakat untuk mengadakan Perjanjian Sewa Menyewa Kendaraan
        ('Perjanjian') dengan kondisi sebagai berikut:</p>
    </td>
    <td class="r">
        <p>The Parties hereby agree to enter into the Car Rental Agreement ('Agreement')
        under the following terms and condition:</p>
    </td>
</tr>

<tr>
    <td><div class="ptitle">PASAL 1<br>DATA-DATA KENDARAAN</div></td>
    <td class="r"><div class="ptitle">ARTICLE 1<br>VEHICLE DATA</div></td>
</tr>

<tr>
    <td>
        <p>PIHAK PERTAMA telah menyerahkan kendaraan untuk disewa oleh PIHAK KEDUA
        dan PIHAK KEDUA telah menerima kendaraan tersebut yang tertuang dalam berita
        acara serah terima kendaraan dan check list yang ditandatangani oleh Para Pihak
        dan merupakan bagian yang tak terpisahkan dari Perjanjian ini. Adapun spesifikasi
        dan jumlah kendaraan tertuang dalam lampiran 1 (satu) Perjanjian ini.</p>
    </td>
    <td class="r">
        <p>The FIRST PARTY shall provide a car to be rented by the SECOND PARTY and
        the SECOND PARTY shall receive the said car that is specifically stated in the
        vehicle handover form and check list form signed by The Parties that constitute
        and inseparable part of this Agreement. The car specification and quantity are
        described in detail in attachment 1 (one) of the Agreement.</p>
    </td>
</tr>
</table>

</div>

{{-- ════════════════════ HALAMAN 2+ : PASAL KETENTUAN DINAMIS ════════════════════ --}}
@php
    // Kelompokkan pasal agar bisa di-render per halaman (bisa multi halaman)
    // Setiap pasal dirender dalam satu page div.
    // Untuk pasal yang sangat panjang, dompdf akan otomatis page-break.
@endphp

@foreach ($pasalKetentuan as $pasalIdx => $pasal)
@php
    $judulIdParts = explode("\n", $pasal['judul_id'] ?? '', 2);
    $judulEnParts = explode("\n", $pasal['judul_en'] ?? '', 2);
    $tipe = $pasal['tipe'] ?? 'list';
    $poinArr = $pasal['poin'] ?? [];
@endphp

<div class="page" style="padding: 16mm 20mm 14mm 20mm;">
<table class="tc" style="font-size:10.5pt; line-height:1.38;">

{{-- Judul Pasal --}}
<tr>
    <td>
        <div class="ptitle">
            {{ strtoupper($judulIdParts[0] ?? '') }}
            @if (!empty($judulIdParts[1]))
                <br>{{ strtoupper($judulIdParts[1]) }}
            @endif
        </div>
    </td>
    <td class="r">
        <div class="ptitle">
            {{ strtoupper($judulEnParts[0] ?? '') }}
            @if (!empty($judulEnParts[1]))
                <br>{{ strtoupper($judulEnParts[1]) }}
            @endif
        </div>
    </td>
</tr>

{{-- Isi Pasal --}}
<tr>
    <td>
        @if ($tipe === 'paragraf')
            @foreach ($poinArr as $p)
                <p class="pre">{{ $replaceTxt($p['id'] ?? '') }}</p>
                <span class="s4"></span>
            @endforeach
        @else
            {{-- list / sublist → numbered --}}
            <ul class="lst" style="margin:2px 0 4px 0;">
                @foreach ($poinArr as $pi => $p)
                    @php $txt = $replaceTxt($p['id'] ?? ''); @endphp
                    <li style="margin-bottom:3px;">
                        <span class="nb">{{ $pi + 1 }}.</span>
                        <span class="tx pre">{{ $txt }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </td>
    <td class="r">
        @if ($tipe === 'paragraf')
            @foreach ($poinArr as $p)
                <p class="pre">{{ $replaceTxt($p['en'] ?? '') }}</p>
                <span class="s4"></span>
            @endforeach
        @else
            <ul class="lst" style="margin:2px 0 4px 0;">
                @foreach ($poinArr as $pi => $p)
                    @php $txt = $replaceTxt($p['en'] ?? ''); @endphp
                    <li style="margin-bottom:3px;">
                        <span class="nb">{{ $pi + 1 }}.</span>
                        <span class="tx pre">{{ $txt }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </td>
</tr>

</table>
</div>

@endforeach

{{-- ════════════════════ HALAMAN TERAKHIR: TTD ════════════════════ --}}
<div class="page" style="padding: 16mm 20mm 14mm 20mm;">
<table class="tc" style="font-size:10.5pt; line-height:1.38;">

{{-- Tanggal --}}
<tr>
    <td><p>Jakarta, {{ $ttdId }}</p></td>
    <td class="r"><p>Jakarta, {{ $ttdEn }}</p></td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

{{-- Label TTD --}}
<tr>
    <td><p>PIHAK PERTAMA/THE FIRST PARTY</p></td>
    <td class="r"><p>PIHAK KEDUA/THE SECOND PARTY</p></td>
</tr>

{{-- Garis & nama TTD --}}
<tr>
    <td>
        <div class="sline"></div>
        <p>{{ $kontrak->pihak_pertama }}</p>
    </td>
    <td class="r">
        <div class="sline"></div>
        <p>………………………………</p>
    </td>
</tr>

</table>
<div class="pgn"></div>
</div>

</body>
</html>
