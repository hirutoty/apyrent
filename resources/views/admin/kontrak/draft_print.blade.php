<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Draft Kontrak {{ $kontrak->no_kontrak }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    color: #000;
    background: #e8e8e8;
    line-height: 1.45;
}

/* ── Wrapper kertas di layar ── */
.page-wrap {
    max-width: 210mm;
    margin: 20px auto 40px;
    background: #fff;
    padding: 15mm 30mm 20mm 30mm;
    box-shadow: 0 3px 16px rgba(0,0,0,0.2);
    min-height: 297mm;
    position: relative;
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

/* ── Judul dokumen ── */
.doc-title { text-align: center; margin-bottom: 14px; }
.doc-title .t1 { font-size: 14pt; font-weight: bold; text-transform: uppercase; display: block; }
.doc-title .t2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; display: block; margin-top: 3px; }
.doc-title .t3 { font-size: 12pt; font-weight: normal; display: block; margin-top: 4px; }

/* ── Dua kolom ── */
.two-col {
    display: table;
    width: 100%;
    border-collapse: collapse;
}
.col-l, .col-r {
    display: table-cell;
    width: 50%;
    vertical-align: top;
    text-align: justify;
}
.col-l { padding-right: 9px; }
.col-r { padding-left:  9px; }

/* ── Header pasal — rata tengah kedua kolom ── */
.pasal-title {
    font-weight: bold;
    text-align: center !important;
    padding-bottom: 4px;
    line-height: 1.5;
}

p { text-align: justify; margin-bottom: 4px; font-size: 12pt; line-height: 1.45; }
.sub { margin: 1px 0 3px 18px; font-size: 11.5pt; line-height: 1.4; }

/* poin bernomor */
.poin-row { display: table; width: 100%; margin-bottom: 4px; }
.poin-num { display: table-cell; width: 24px; vertical-align: top; white-space: nowrap; font-size: 12pt; line-height: 1.45; }
.poin-txt { display: table-cell; vertical-align: top; text-align: justify; font-size: 12pt; line-height: 1.45; }

/* spacer */
.gap4  { height: 4px;  display: block; }
.gap8  { height: 8px;  display: block; }
.gap12 { height: 12px; display: block; }
.gap20 { height: 20px; display: block; }
.gap28 { height: 28px; display: block; }

.sline { border-top: 1px solid #000; width: 200px; margin-top: 52px; margin-bottom: 5px; }

/* ── Nomor halaman screen preview ── */
.page-num-preview {
    position: absolute;
    bottom: 8mm;
    left: 0; right: 0;
    text-align: center;
    font-size: 10pt;
    color: #999;
    font-family: "Times New Roman", serif;
}

/* ── PRINT ── */
@media print {
    body { background: #fff; padding-top: 0; }
    .toolbar { display: none !important; }
    .page-wrap {
        max-width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
        min-height: unset;
    }
    .page-num-preview { display: none; }
    @page {
        size: A4;
        margin: 15mm 30mm 15mm 30mm;
        @bottom-center {
            content: counter(page);
            font-family: "Times New Roman", serif;
            font-size: 10pt;
        }
    }
}
</style>
</head>
<body>

{{-- Toolbar screen-only --}}
<div class="toolbar">
    <span style="font-weight:600;">Draft Kontrak</span>
    <span style="color:#94a3b8;">{{ $kontrak->no_kontrak }}</span>
    <a href="{{ route('kontrak.index') }}">← Kembali</a>
    <span class="toolbar-spacer"></span>
    <button class="btn-print" onclick="window.print()">🖨 Print / Save PDF (Ctrl+P)</button>
</div>

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
$rp = function(string $t) use ($ph) {
    return str_replace(array_keys($ph), array_values($ph), $t);
};

// ── Parse plain text → array pasal ──
$parsePasal = function(string $raw) use ($rp) {
    $pasals = [];
    $curNum = null;
    $cur    = null;
    foreach (explode("\n", $raw) as $line) {
        $tr = trim($line);
        if ($tr === '') continue;
        if (preg_match('/^(?:PASAL|ARTICLE)\s+(\d+)$/i', $tr, $m)) {
            if ($cur !== null) $pasals[$curNum] = $cur;
            $curNum = (int)$m[1];
            $cur    = ['judul' => $tr, 'items' => []];
            continue;
        }
        if ($cur === null) { $curNum = 0; $cur = ['judul' => '', 'items' => []]; }
        if (strtoupper($tr) === $tr && strlen($tr) > 2 && empty($cur['items'])
            && !preg_match('/^\d+\./', $tr) && !preg_match('/^[a-z]\./', $tr) && !preg_match('/^-/', $tr)) {
            $cur['judul'] .= "\n" . $tr;
            continue;
        }
        if (preg_match('/^(\d+|[a-z])\.\s+(.+)/u', $tr, $m)) {
            $cur['items'][] = ['type' => 'poin', 'num' => $m[1], 'text' => $rp($m[2])];
            continue;
        }
        if (preg_match('/^-\s+(.+)/u', $tr, $m)) {
            $cur['items'][] = ['type' => 'sub', 'text' => $rp($m[1])];
            continue;
        }
        $cur['items'][] = ['type' => 'teks', 'text' => $rp($tr)];
    }
    if ($cur !== null) $pasals[$curNum] = $cur;
    return $pasals;
};

// ── Render satu item → HTML ──
$renderItem = function(array $item) {
    $t = htmlspecialchars($item['text'] ?? '');
    if ($item['type'] === 'poin') {
        $n = htmlspecialchars($item['num'] ?? '');
        return '<div class="poin-row"><span class="poin-num">' . $n . '.</span><span class="poin-txt">' . $t . '</span></div>';
    }
    if ($item['type'] === 'sub') {
        return '<p class="sub">- ' . $t . '</p>';
    }
    return '<p>' . $t . '</p>';
};

// ── Render format lama (pasal JSON) ──
$renderPoin = function(array $poinArr, string $lang, string $tipe, callable $rp) {
    $out = '';
    if ($tipe === 'paragraf') {
        foreach ($poinArr as $p) {
            $lines = explode("\n", $rp($p[$lang] ?? ''));
            foreach ($lines as $idx => $line) {
                $tr = trim($line);
                if ($tr === '') { $out .= '<span class="gap4"></span>'; continue; }
                if ($idx > 0 && preg_match('/^-\s+(.+)/u', $tr, $m))
                    $out .= '<p class="sub">- ' . htmlspecialchars($m[1]) . '</p>';
                elseif ($idx > 0 && preg_match('/^[A-Za-z]\.\s/', $tr))
                    $out .= '<p class="sub">' . htmlspecialchars($tr) . '</p>';
                else
                    $out .= '<p>' . htmlspecialchars($tr) . '</p>';
            }
            $out .= '<span class="gap4"></span>';
        }
    } else {
        foreach ($poinArr as $pi => $p) {
            $lines    = explode("\n", $rp($p[$lang] ?? ''));
            $mt       = array_shift($lines);
            $subLines = array_filter($lines, function($l) { return trim($l) !== ''; });
            $out .= '<div class="poin-row"><span class="poin-num">' . ($pi+1) . '.</span><span class="poin-txt">' . htmlspecialchars($mt);
            foreach ($subLines as $sl) {
                $out .= '<br><span style="display:inline-block;padding-left:12px;font-size:11pt;">'
                      . htmlspecialchars(ltrim($sl)) . '</span>';
            }
            $out .= '</span></div>';
        }
    }
    return $out;
};

$useNewFormat = !empty($kontrak->ketentuan_id) || !empty($kontrak->ketentuan_en);

if ($useNewFormat) {
    $rawId      = $rp($kontrak->ketentuan_id ?? KontrakHelper::defaultPlainText('id'));
    $rawEn      = $rp($kontrak->ketentuan_en ?? KontrakHelper::defaultPlainText('en'));
    $pasalsId   = $parsePasal($rawId);
    $pasalsEn   = $parsePasal($rawEn);
    $nomorPasal = array_unique(array_merge(array_keys($pasalsId), array_keys($pasalsEn)));
    sort($nomorPasal);
    $pasalKetentuan = [];
} else {
    $pasalKetentuan = $kontrak->pasal_ketentuan ?: KontrakHelper::defaultPasalKetentuan();
    $pasalsId = $pasalsEn = [];
    $nomorPasal = [];
}

// ── Build HTML ketentuan ──
$htmlKetentuan = '';
if ($useNewFormat) {
    foreach ($nomorPasal as $idx => $num) {
        $pId = isset($pasalsId[$num]) ? $pasalsId[$num] : ['judul' => '', 'items' => []];
        $pEn = isset($pasalsEn[$num]) ? $pasalsEn[$num] : ['judul' => '', 'items' => []];

        $judulIdParts = array_filter(array_map('trim', explode("\n", $pId['judul'])));
        $judulEnParts = array_filter(array_map('trim', explode("\n", $pEn['judul'])));

        if ($idx > 0) $htmlKetentuan .= '<div class="gap28"></div>';

        // Judul
        $htmlKetentuan .= '<div class="two-col">'
            . '<div class="col-l pasal-title">' . implode('<br>', array_map('htmlspecialchars', $judulIdParts)) . '</div>'
            . '<div class="col-r pasal-title">' . implode('<br>', array_map('htmlspecialchars', $judulEnParts)) . '</div>'
            . '</div>';

        // Isi: satu baris per item — browser handle perbedaan tinggi dengan baik
        $itemsId  = $pId['items'];
        $itemsEn  = $pEn['items'];
        $maxItems = max(count($itemsId), count($itemsEn));

        for ($ii = 0; $ii < $maxItems; $ii++) {
            $iId    = isset($itemsId[$ii]) ? $itemsId[$ii] : null;
            $iEn    = isset($itemsEn[$ii]) ? $itemsEn[$ii] : null;
            $htmlId = $iId ? $renderItem($iId) : '';
            $htmlEn = $iEn ? $renderItem($iEn) : '';
            $htmlKetentuan .= '<div class="two-col">'
                . '<div class="col-l">' . $htmlId . '</div>'
                . '<div class="col-r">' . $htmlEn . '</div>'
                . '</div>';
        }
    }
} else {
    foreach ($pasalKetentuan as $pi => $pasal) {
        $jId    = strtoupper(str_replace("\n", '<br>', $pasal['judul_id'] ?? ''));
        $jEn    = strtoupper(str_replace("\n", '<br>', $pasal['judul_en'] ?? ''));
        $poinId = $renderPoin($pasal['poin'] ?? [], 'id', $pasal['tipe'] ?? 'list', $rp);
        $poinEn = $renderPoin($pasal['poin'] ?? [], 'en', $pasal['tipe'] ?? 'list', $rp);

        if ($pi > 0) $htmlKetentuan .= '<div class="gap28"></div>';

        $htmlKetentuan .= '<div class="two-col">'
            . '<div class="col-l pasal-title">' . $jId . '</div>'
            . '<div class="col-r pasal-title">' . $jEn . '</div>'
            . '</div>';

        $htmlKetentuan .= '<div class="two-col">'
            . '<div class="col-l">' . $poinId . '</div>'
            . '<div class="col-r">' . $poinEn . '</div>'
            . '</div>';
    }
}
@endphp

<div class="page-wrap">

    {{-- Judul --}}
    <div class="doc-title">
        <span class="t1">PERJANJIAN SEWA MENYEWA KENDARAAN</span>
        <span class="t2">CAR RENTAL AGREEMENT</span>
        <span class="t3">NO : {{ $kontrak->no_kontrak }}</span>
    </div>

    {{-- Pembuka --}}
    <div class="two-col">
        <div class="col-l"><p>Pada hari ini, {{ $hariId }}, tanggal {{ $tglId }}, kami yang bertanda tangan dibawah ini, masing-masing:</p></div>
        <div class="col-r"><p>On this day, {{ $hariEn }}, {{ $tglEn }}, we the undersigned, respectively:</p></div>
    </div>
    <div class="gap12"></div>

    <div class="two-col">
        <div class="col-l"><p><strong>1. {{ $namaPerush }},</strong> suatu perseroan terbatas yang memiliki kantor terdaftar di {{ $alamatPerush }}, dalam hal ini diwakili oleh {{ $kontrak->pihak_pertama }}, jabatan {{ $pihak1Jabatan }}, pemegang KTP No. {{ $pihak1KTP }}, selanjutnya disebut "Pihak Pertama"; dan</p></div>
        <div class="col-r"><p><strong>1. {{ $namaPerush }},</strong> a limited liability company, having its registered office at {{ $alamatPerush }}, in this matter represented by {{ $kontrak->pihak_pertama }}, acting as {{ $pihak1Jabatan }}, a holder of identity card no. {{ $pihak1KTP }}, hereinafter referred to as the "First Party"; and</p></div>
    </div>
    <div class="gap12"></div>

    <div class="two-col">
        <div class="col-l"><p><strong>2. {{ $namaP2 }},</strong> yang beralamat di {{ $alamatP2 }}, pemegang KTP No. {{ $noktp2 }}, selanjutnya disebut sebagai "Pihak Kedua".</p></div>
        <div class="col-r"><p><strong>2. {{ $namaP2 }},</strong> domiciled at {{ $alamatP2 }}, holder of identity card no. {{ $noktp2 }}, hereinafter referred to as the "Second Party".</p></div>
    </div>
    <div class="gap12"></div>

    <div class="two-col">
        <div class="col-l"><p>Para Pihak sepakat untuk mengadakan Perjanjian Sewa Menyewa Kendaraan ('Perjanjian') dengan kondisi sebagai berikut:</p></div>
        <div class="col-r"><p>The Parties hereby agree to enter into the Car Rental Agreement ('Agreement') under the following terms and condition:</p></div>
    </div>
    <div class="gap20"></div>

    {{-- Ketentuan --}}
    {!! $htmlKetentuan !!}

    {{-- TTD --}}
    <div class="gap28"></div>
    <div class="two-col">
        <div class="col-l"><p>Jakarta, {{ $ttdId }}</p></div>
        <div class="col-r"><p>Jakarta, {{ $ttdEn }}</p></div>
    </div>
    <div class="gap8"></div>
    <div class="two-col">
        <div class="col-l"><p>PIHAK PERTAMA/THE FIRST PARTY</p></div>
        <div class="col-r"><p>PIHAK KEDUA/THE SECOND PARTY</p></div>
    </div>
    <div class="two-col">
        <div class="col-l"><div class="sline"></div><p>{{ $kontrak->pihak_pertama }}</p></div>
        <div class="col-r"><div class="sline"></div><p>{{ $namaP2 }}</p></div>
    </div>

</div>{{-- /page-wrap --}}

</body>
</html>
