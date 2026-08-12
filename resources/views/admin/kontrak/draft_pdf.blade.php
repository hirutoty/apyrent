<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Perjanjian MenyewaT - {{ $kontrak->no_kontrak }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    color: #000;
    background: #fff;
    line-height: 1.45;
    margin: 0;
    padding: 0;
}

@page { 
    margin: 5mm 25mm 5mm 25mm;
    size: A4;
}

/* Additional body padding for consistent margins */
body {
    padding: 0 15mm;
}

/* ── JUDUL DOKUMEN ── */
.doc-title { text-align: center; margin-bottom: 12px; }
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
    word-break: break-word;
    overflow-wrap: break-word;
    padding: 0 6px 0 0;
    font-size: 11pt;
    line-height: 1.45;
}
.tc td.r { padding: 0 0 0 6px; }

/* ── PARAGRAF ── */
p { text-align: justify; margin-bottom: 5px; font-size: 11pt; }

/* ── NUMBERED LIST ── */
.lst { list-style: none; padding: 0; margin: 4px 0 6px 0; }
.lst > li { display: table; width: 100%; margin-bottom: 5px; text-align: justify; font-size: 11pt; line-height: 1.45; }
.lst > li > .nb { display: table-cell; width: 22px; vertical-align: top; white-space: nowrap; }
.lst > li > .tx { display: table-cell; vertical-align: top; text-align: justify; }

/* ── SPACERS ── */
.s4  { height: 4px;  display: block; }
.s8  { height: 8px;  display: block; }
.s12 { height: 12px; display: block; }
.s18 { height: 18px; display: block; }

/* ── NOMOR HALAMAN ── */
.pgn { position: fixed; bottom: 6mm; left: 0; right: 0; text-align: center; font-size: 11pt; }
.pgn:after { content: counter(page); }

/* ── GARIS TTD ── */
.sline { border-top: 1px solid #000; width: 210px; margin-top: 40px; margin-bottom: 3px; }
</style>
</head>
<body>
@php
    use Carbon\Carbon;
    use App\Helpers\KontrakHelper;
    Carbon::setLocale('id');

    $tgl    = Carbon::parse($kontrak->tanggal_kontrak);
    $hariId = $tgl->isoFormat('dddd');
    $tglId  = $tgl->isoFormat('D MMMM YYYY');
    $hariEn = $tgl->locale('en')->isoFormat('dddd');
    $tglEn  = $tgl->locale('en')->isoFormat('D MMMM YYYY');
    Carbon::setLocale('id');

    $dV = $kontrak->durasi_value  ?? '';
    $dS = $kontrak->durasi_satuan ? ucfirst($kontrak->durasi_satuan) : '';

    $mulaiId   = $kontrak->tanggal_kontrak ? Carbon::parse($kontrak->tanggal_kontrak)->isoFormat('D MMMM YYYY') : '..........';
    $selesaiId = $kontrak->tanggal_selesai ? Carbon::parse($kontrak->tanggal_selesai)->isoFormat('D MMMM YYYY') : '..........';
    $mulaiEn   = $kontrak->tanggal_kontrak ? Carbon::parse($kontrak->tanggal_kontrak)->locale('en')->isoFormat('D MMMM YYYY') : '..........';
    $selesaiEn = $kontrak->tanggal_selesai ? Carbon::parse($kontrak->tanggal_selesai)->locale('en')->isoFormat('D MMMM YYYY') : '..........';
    Carbon::setLocale('id');

    $namaPerush   = $setting->nama_perusahaan    ?? 'PT. Anugerah Panca Yoga';
    $alamatPerush = $setting->alamat             ?? 'Jl. Catur No. 16, Menteng Dalam, Tebet, Jakarta Selatan 12870';
    $telpPerush   = $setting->telepon            ?? '021 - 83792927';
    $faxPerush    = $setting->fax                ?? '021 - 8354565';
    $namaBank     = $setting->nama_bank          ?? 'BCA';
    $noRek        = $setting->nomor_rekening     ?? '272-1420-878';
    $atasNama     = $setting->atas_nama_rekening ?? $kontrak->pihak_pertama;

    $ttdId = $tgl->isoFormat('D MMMM YYYY');
    $ttdEn = $tgl->locale('en')->isoFormat('D MMMM YYYY');
    Carbon::setLocale('id');

    $pihak1Jabatan = 'General Manager';
    $pihak1KTP     = $kontrak->contact_pertama ?? '…………………………………';

    $namaP2   = $kontrak->pihak_kedua
                ?: ($kontrak->penawaran->customer_name ?? null)
                ?: ($kontrak->penawaran->kepada ?? '…………………………………');
    $alamatP2 = ($kontrak->alamat_kedua ?: null) ?? ($kontrak->penawaran->alamat ?? null);
    $noktp2   = ($kontrak->no_ktp_kedua ?: null) ?? ($kontrak->penawaran->no_ktp ?? null);

    if (!$alamatP2 || !$noktp2) {
        $pelP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->pihak_kedua ?? '')->first();
        if (!$pelP2 && !empty($kontrak->penawaran->customer_name))
            $pelP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->penawaran->customer_name)->first();
        if (!$pelP2 && !empty($kontrak->penawaran->kepada))
            $pelP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->penawaran->kepada)->first();
        $alamatP2 = $alamatP2 ?? $pelP2?->alamat ?? '…………………………………………………………………………';
        $noktp2   = $noktp2   ?? $pelP2?->no_ktp ?? '……………………………………';
    }

    $ppn = $setting->ppn_default ?? 11;
    $pph = $setting->pph_default ?? 2;

    $pasalKetentuan = $kontrak->pasal_ketentuan ?: KontrakHelper::defaultPasalKetentuan();
    $pasal1    = $pasalKetentuan[0] ?? null;
    $pasalRest = array_slice($pasalKetentuan, 1);

    $durasiStr = ($dV && $dS) ? "$dV $dS" : '…………';
    $ph = [
        '{DURASI}'             => $durasiStr,
        '{TANGGAL_MULAI}'      => $mulaiId,
        '{TANGGAL_SELESAI}'    => $selesaiId,
        '{TANGGAL_MULAI_EN}'   => $mulaiEn,
        '{TANGGAL_SELESAI_EN}' => $selesaiEn,
        '{PPN}'                => $ppn,
        '{PPH}'                => $pph,
        '{NAMA_BANK}'          => $namaBank,
        '{NO_REKENING}'        => $noRek,
        '{ATAS_NAMA}'          => $atasNama,
        '{NAMA_PERUSAHAAN}'    => $namaPerush,
        '{ALAMAT_PERUSAHAAN}'  => $alamatPerush,
        '{TELEPON_PERUSAHAAN}' => $telpPerush,
        '{FAX_PERUSAHAAN}'     => $faxPerush,
        '{NAMA_PIHAK_KEDUA}'   => $namaP2,
        '{ALAMAT_PIHAK_KEDUA}' => $alamatP2,
        '{KONTAK_PIHAK_KEDUA}' => $kontrak->contact_kedua ?? '…..........................',
    ];

    $rp = function(string $txt) use ($ph): string {
        return str_replace(array_keys($ph), array_values($ph), $txt);
    };

    // Helper render isi pasal (paragraf/list) — ID atau EN
    $renderPoin = function(array $poinArr, string $lang, string $tipe, callable $rp): string {
        $out = '';
        if ($tipe === 'paragraf') {
            foreach ($poinArr as $p) {
                $lines = explode("\n", $rp($p[$lang] ?? ''));
                $fl    = array_shift($lines);
                $out  .= '<p style="margin-bottom:3px;">' . htmlspecialchars($fl) . '</p>';
                foreach ($lines as $line) {
                    $tr = ltrim($line);
                    if ($tr === '') {
                        $out .= '<span class="s4"></span>';
                    } elseif (preg_match('/^[-a-zA-Z]\.?\s/', $tr)) {
                        $out .= '<p style="margin:1px 0 1px 12px;font-size:10.5pt;">' . htmlspecialchars($tr) . '</p>';
                    } else {
                        $out .= '<p style="margin-bottom:2px;">' . htmlspecialchars($tr) . '</p>';
                    }
                }
                $out .= '<span class="s4"></span>';
            }
        } else {
            $out .= '<ul class="lst" style="margin:2px 0 4px 0;">';
            foreach ($poinArr as $pi => $p) {
                $lines    = explode("\n", $rp($p[$lang] ?? ''));
                $mt       = array_shift($lines);
                $subLines = array_filter($lines, fn($l) => trim($l) !== '');
                $out .= '<li style="margin-bottom:4px;"><span class="nb">' . ($pi + 1) . '.</span><span class="tx">' . htmlspecialchars($mt);
                foreach ($subLines as $sl) {
                    $out .= '<br><span style="display:inline-block;padding-left:8px;">' . htmlspecialchars(ltrim($sl)) . '</span>';
                }
                $out .= '</span></li>';
            }
            $out .= '</ul>';
        }
        return $out;
    };
@endphp

{{-- ═══════════════════════════════════════════════════════
     HALAMAN 1: Para Pihak + Pasal 1
═══════════════════════════════════════════════════════ --}}
<div style="page-break-after:always;">

    <div class="doc-title">
        <span class="t1">PERJANJIAN SEWA MENYEWA KENDARAAN</span>
        <span class="t2">CAR RENTAL AGREEMENT</span>
        <span class="t3">NO : {{ $kontrak->no_kontrak }}</span>
    </div>

    <table class="tc">

    {{-- Tanggal & pembuka --}}
    <tr>
        <td><p>Pada hari ini, {{ $hariId }}, tanggal {{ $tglId }}, kami yang bertanda tangan dibawah ini, masing-masing:</p></td>
        <td class="r"><p>On this day, {{ $hariEn }}, {{ $tglEn }}, we the undersigned, respectively:</p></td>
    </tr>
    <tr><td colspan="2"><span class="s8"></span></td></tr>

    {{-- Pihak Pertama --}}
    <tr>
        <td>
            <p><strong>1. {{ $namaPerush }},</strong> suatu perseroan terbatas yang memiliki kantor terdaftar di {{ $alamatPerush }}, dalam hal ini diwakili oleh {{ $kontrak->pihak_pertama }}, jabatan {{ $pihak1Jabatan }}, pemegang KTP No. {{ $pihak1KTP }}, selanjutnya disebut "Pihak Pertama"; dan</p>
        </td>
        <td class="r">
            <p><strong>1. {{ $namaPerush }},</strong> a limited liability company, having its registered office at {{ $alamatPerush }}, in this matter represented by {{ $kontrak->pihak_pertama }}, acting as {{ $pihak1Jabatan }}, a holder of identity card no. {{ $pihak1KTP }}, hereinafter referred to as the "First Party"; and</p>
        </td>
    </tr>
    <tr><td colspan="2"><span class="s8"></span></td></tr>

    {{-- Pihak Kedua --}}
    <tr>
        <td>
            <p><strong>2. {{ $namaP2 }},</strong> yang beralamat di {{ $alamatP2 }}, pemegang KTP No. {{ $noktp2 }}, selanjutnya disebut sebagai "Pihak Kedua".</p>
        </td>
        <td class="r">
            <p><strong>2. {{ $namaP2 }},</strong> domiciled at {{ $alamatP2 }}, holder of identity card no. {{ $noktp2 }}, hereinafter referred to as the "Second Party".</p>
        </td>
    </tr>
    <tr><td colspan="2"><span class="s8"></span></td></tr>

    {{-- Kalimat kesepakatan --}}
    <tr>
        <td><p>Para Pihak sepakat untuk mengadakan Perjanjian Sewa Menyewa Kendaraan ('Perjanjian') dengan kondisi sebagai berikut:</p></td>
        <td class="r"><p>The Parties hereby agree to enter into the Car Rental Agreement ('Agreement') under the following terms and condition:</p></td>
    </tr>

    {{-- Pasal 1 --}}
    @if($pasal1)
    @php
        $j1Id = strtoupper(str_replace("\n", '<br/>', $pasal1['judul_id'] ?? ''));
        $j1En = strtoupper(str_replace("\n", '<br/>', $pasal1['judul_en'] ?? ''));
    @endphp
    <tr><td colspan="2"><span class="s12"></span></td></tr>
    <tr>
        <td style="text-align:center;font-weight:bold;font-size:11pt;padding:6px 10px 3px 0;line-height:1.5;">{!! $j1Id !!}</td>
        <td style="text-align:center;font-weight:bold;font-size:11pt;padding:6px 0 3px 10px;line-height:1.5;">{!! $j1En !!}</td>
    </tr>
    <tr>
        <td>{!! $renderPoin($pasal1['poin'] ?? [], 'id', $pasal1['tipe'] ?? 'paragraf', $rp) !!}</td>
        <td class="r">{!! $renderPoin($pasal1['poin'] ?? [], 'en', $pasal1['tipe'] ?? 'paragraf', $rp) !!}</td>
    </tr>
    @endif

    </table>
</div>

{{-- ═══════════════════════════════════════════════════════
     HALAMAN 2+: Pasal 2 dst + TTD (tanpa header kontrak)
═══════════════════════════════════════════════════════ --}}
<div>

    <table class="tc" style="font-size:10.5pt;line-height:1.38;">

    @foreach($pasalRest as $pi => $pasal)
    @php
        $jId = strtoupper(str_replace("\n", '<br/>', $pasal['judul_id'] ?? ''));
        $jEn = strtoupper(str_replace("\n", '<br/>', $pasal['judul_en'] ?? ''));
    @endphp
    @if($pi > 0)
    <tr><td colspan="2"><span class="s12"></span></td></tr>
    @endif
    <tr>
        <td style="text-align:center;font-weight:bold;font-size:11pt;padding:6px 10px 3px 0;line-height:1.5;">{!! $jId !!}</td>
        <td style="text-align:center;font-weight:bold;font-size:11pt;padding:6px 0 3px 10px;line-height:1.5;">{!! $jEn !!}</td>
    </tr>
    <tr>
        <td>{!! $renderPoin($pasal['poin'] ?? [], 'id', $pasal['tipe'] ?? 'list', $rp) !!}</td>
        <td class="r">{!! $renderPoin($pasal['poin'] ?? [], 'en', $pasal['tipe'] ?? 'list', $rp) !!}</td>
    </tr>
    @endforeach

    {{-- TTD --}}
    <tr><td colspan="2"><span class="s18"></span></td></tr>
    <tr>
        <td><p>Jakarta, {{ $ttdId }}</p></td>
        <td class="r"><p>Jakarta, {{ $ttdEn }}</p></td>
    </tr>
    <tr><td colspan="2"><span class="s8"></span></td></tr>
    <tr>
        <td><p>PIHAK PERTAMA/THE FIRST PARTY</p></td>
        <td class="r"><p>PIHAK KEDUA/THE SECOND PARTY</p></td>
    </tr>
    <tr>
        <td>
            <div class="sline"></div>
            <p>{{ $kontrak->pihak_pertama }}</p>
        </td>
        <td class="r">
            <div class="sline"></div>
            <p>{{ $namaP2 }}</p>
        </td>
    </tr>

    </table>
</div>

<div class="pgn"></div>

</body>
</html>

