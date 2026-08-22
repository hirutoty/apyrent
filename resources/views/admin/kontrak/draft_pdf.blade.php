<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Perjanjian Sewa Menyewa - {{ $kontrak->no_kontrak }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; word-wrap: break-word; overflow-wrap: break-word; }
@page { margin: 5mm 25mm 5mm 25mm; size: A4; }
body { font-family: "Times New Roman", Times, serif; font-size: 11pt; color: #000; background: #fff; line-height: 1.55; }
.doc-title { text-align: center; margin-bottom: 10px; }
.doc-title .t1 { font-size: 13pt; font-weight: bold; text-transform: uppercase; display: block; }
.doc-title .t2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; display: block; margin-top: 2px; }
.doc-title .t3 { font-size: 11pt; font-weight: normal; display: block; margin-top: 3px; }
/* A4=210mm, margin 25mm×2=50mm → konten=160mm */
.tc { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 0; }
.tc td { width: 50%; vertical-align: top; text-align: justify; padding: 0 5px 0 0; font-size: 11pt; line-height: 1.55; word-wrap: break-word; overflow-wrap: break-word; }
.tc td.r { padding: 0 0 0 5px; }
p { text-align: justify; margin-bottom: 4px; font-size: 11pt; line-height: 1.55; }
.sub { margin: 1px 0 2px 14px; font-size: 10.5pt; line-height: 1.5; }
.gap4  { height: 4px; }
.gap8  { height: 8px; }
.gap12 { height: 12px; }
.gap18 { height: 18px; }
.sline { border-top: 1px solid #000; width: 200px; margin-top: 45px; margin-bottom: 4px; }
.pgn { position: fixed; bottom: 4mm; left: 0; right: 0; text-align: center; font-size: 10pt; }
.pgn:after { content: counter(page); }
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

// ── Parse plain text → array pasal terstruktur ──
// nomor pasal (int) => ['judul'=>str, 'items'=>[...]]
$parsePasal = function(string $raw) use ($rp) {
    $pasals = [];
    $curNum = null;
    $cur    = null;
    foreach (explode("\n", $raw) as $line) {
        $tr = trim($line);
        // "PASAL N" atau "ARTICLE N"
        if (preg_match('/^(?:PASAL|ARTICLE)\s+(\d+)$/i', $tr, $m)) {
            if ($cur !== null) $pasals[$curNum] = $cur;
            $curNum = (int)$m[1];
            $cur    = ['judul' => $tr, 'items' => []];
            continue;
        }
        if ($cur === null) { $curNum = 0; $cur = ['judul' => '', 'items' => []]; }
        // Sub-judul: semua caps, sebelum ada item
        if (strtoupper($tr) === $tr && strlen($tr) > 2
            && empty($cur['items'])
            && !preg_match('/^\d+\./', $tr)
            && !preg_match('/^[a-z]\./', $tr)
            && !preg_match('/^-/', $tr)) {
            $cur['judul'] .= "\n" . $tr;
            continue;
        }
        // Baris kosong → simpan sebagai blank
        if ($tr === '') {
            $cur['items'][] = ['type' => 'blank'];
            continue;
        }
        // Poin bernomor
        if (preg_match('/^(\d+|[a-z])\.\s+(.+)/u', $tr, $m)) {
            $cur['items'][] = ['type' => 'poin', 'num' => $m[1], 'text' => $rp($m[2])];
            continue;
        }
        // Sub-poin
        if (preg_match('/^-\s+(.+)/u', $tr, $m)) {
            $cur['items'][] = ['type' => 'sub', 'text' => $rp($m[1])];
            continue;
        }
        // Baris lanjutan — cari poin terakhir (lewati sub di antaranya, stop di blank)
        $lastPoinIdx = null;
        for ($li = count($cur['items']) - 1; $li >= 0; $li--) {
            if ($cur['items'][$li]['type'] === 'poin') { $lastPoinIdx = $li; break; }
            if ($cur['items'][$li]['type'] === 'blank') break;
        }
        if ($lastPoinIdx !== null) {
            $cur['items'][$lastPoinIdx]['text'] .= "\n" . $rp($tr);
            continue;
        }
        // Teks biasa
        $cur['items'][] = ['type' => 'teks', 'text' => $rp($tr)];
    }
    if ($cur !== null) $pasals[$curNum] = $cur;
    return $pasals;
};

// ── Render satu item → HTML ──
$renderItem = function(array $item) {
    if ($item['type'] === 'blank') {
        return '<p style="margin:4px 0;"> </p>';
    }
    $raw = $item['text'] ?? '';
    // Hilangkan baris yang dimulai dengan "Hp."
    if (preg_match('/^Hp\./i', trim($raw))) {
        return '';
    }
    if ($item['type'] === 'poin') {
        $n     = htmlspecialchars($item['num'] ?? '');
        $lines = explode("\n", $raw);
        $first = htmlspecialchars(array_shift($lines));
        $rest  = '';
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln === '') continue;
            $rest .= '<br>' . htmlspecialchars($ln);
        }
        return '<table style="width:100%;border-collapse:collapse;table-layout:fixed;">'
             . '<tr><td style="width:16px;vertical-align:top;white-space:nowrap;padding:0;">' . $n . '.</td>'
             . '<td style="vertical-align:top;text-align:justify;padding:0 0 0 3px;">' . $first . $rest . '</td></tr></table>';
    }
    if ($item['type'] === 'sub') {
        return '<p class="sub">- ' . htmlspecialchars($raw) . '</p>';
    }
    // teks all-caps → bold
    if (strtoupper($raw) === $raw && strlen(trim($raw)) > 3) {
        return '<p style="font-weight:bold;">' . htmlspecialchars($raw) . '</p>';
    }
    return '<p>' . htmlspecialchars($raw) . '</p>';
};

// ── Render format lama (pasal JSON) ──
$renderPoin = function(array $poinArr, string $lang, string $tipe, callable $rp) {
    $out = '';
    if ($tipe === 'paragraf') {
        foreach ($poinArr as $p) {
            $lines = explode("\n", $rp($p[$lang] ?? ''));
            foreach ($lines as $idx => $line) {
                $tr = trim($line);
                if ($tr === '') { $out .= '<div class="gap4"></div>'; continue; }
                if ($idx > 0 && preg_match('/^-\s+(.+)/u', $tr, $m))
                    $out .= '<p class="sub">- ' . htmlspecialchars($m[1]) . '</p>';
                elseif ($idx > 0 && preg_match('/^[A-Za-z]\.\s/', $tr))
                    $out .= '<p class="sub">' . htmlspecialchars($tr) . '</p>';
                else
                    $out .= '<p>' . htmlspecialchars($tr) . '</p>';
            }
            $out .= '<div class="gap4"></div>';
        }
    } else {
        foreach ($poinArr as $pi => $p) {
            $lines    = explode("\n", $rp($p[$lang] ?? ''));
            $mt       = array_shift($lines);
            $subLines = array_filter($lines, function($l) { return trim($l) !== ''; });
            $out .= '<table style="width:100%;border-collapse:collapse;table-layout:fixed;">'
                  . '<tr><td style="width:22px;vertical-align:top;white-space:nowrap;padding:0;">' . ($pi+1) . '.</td>'
                  . '<td style="vertical-align:top;text-align:justify;padding:0;">' . htmlspecialchars($mt);
            foreach ($subLines as $sl) {
                $out .= '<br><span style="display:inline-block;padding-left:10px;font-size:10.5pt;">'
                      . htmlspecialchars(ltrim($sl)) . '</span>';
            }
            $out .= '</td></tr></table>';
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
@endphp

<div class="doc-title">
    <span class="t1">PERJANJIAN SEWA MENYEWA KENDARAAN</span>
    <span class="t2">CAR RENTAL AGREEMENT</span>
    <span class="t3">NO : {{ $kontrak->no_kontrak }}</span>
</div>

<table class="tc"><tr>
    <td><p>Pada hari ini, {{ $hariId }}, tanggal {{ $tglId }}, kami yang bertanda tangan dibawah ini, masing-masing:</p></td>
    <td class="r"><p>On this day, {{ $hariEn }}, {{ $tglEn }}, we the undersigned, respectively:</p></td>
</tr></table>
<div class="gap8"></div>

<table class="tc"><tr>
    <td><p><strong>1. {{ $namaPerush }},</strong> suatu perseroan terbatas yang memiliki kantor terdaftar di {{ $alamatPerush }}, dalam hal ini diwakili oleh {{ $kontrak->pihak_pertama }}, jabatan {{ $pihak1Jabatan }}, pemegang KTP No. {{ $pihak1KTP }}, selanjutnya disebut "Pihak Pertama"; dan</p></td>
    <td class="r"><p><strong>1. {{ $namaPerush }},</strong> a limited liability company, having its registered office at {{ $alamatPerush }}, in this matter represented by {{ $kontrak->pihak_pertama }}, acting as {{ $pihak1Jabatan }}, a holder of identity card no. {{ $pihak1KTP }}, hereinafter referred to as the "First Party"; and</p></td>
</tr></table>
<div class="gap8"></div>

<table class="tc"><tr>
    <td><p><strong>2. {{ $namaP2 }},</strong> yang beralamat di {{ $alamatP2 }}, pemegang KTP No. {{ $noktp2 }}, selanjutnya disebut sebagai "Pihak Kedua".</p></td>
    <td class="r"><p><strong>2. {{ $namaP2 }},</strong> domiciled at {{ $alamatP2 }}, holder of identity card no. {{ $noktp2 }}, hereinafter referred to as the "Second Party".</p></td>
</tr></table>
<div class="gap8"></div>

<table class="tc"><tr>
    <td><p>Para Pihak sepakat untuk mengadakan Perjanjian Sewa Menyewa Kendaraan ('Perjanjian') dengan kondisi sebagai berikut:</p></td>
    <td class="r"><p>The Parties hereby agree to enter into the Car Rental Agreement ('Agreement') under the following terms and condition:</p></td>
</tr></table>
<div class="gap12"></div>

@php
// ── Bangun seluruh HTML ketentuan di PHP (hindari @for/@else konflik Blade) ──
$htmlKetentuan = '';

if ($useNewFormat) {
    foreach ($nomorPasal as $idx => $num) {
        $pId = isset($pasalsId[$num]) ? $pasalsId[$num] : ['judul' => '', 'items' => []];
        $pEn = isset($pasalsEn[$num]) ? $pasalsEn[$num] : ['judul' => '', 'items' => []];

        $judulIdParts = array_filter(array_map('trim', explode("\n", $pId['judul'])));
        $judulEnParts = array_filter(array_map('trim', explode("\n", $pEn['judul'])));

        if ($idx > 0) $htmlKetentuan .= '<div class="gap18"></div>';

        // Judul
        $htmlKetentuan .= '<table class="tc"><tr>'
            . '<td style="text-align:center;font-weight:bold;padding-bottom:2px;">'
            . implode('<br>', array_map('htmlspecialchars', $judulIdParts))
            . '</td>'
            . '<td class="r" style="text-align:center;font-weight:bold;padding-bottom:2px;">'
            . implode('<br>', array_map('htmlspecialchars', $judulEnParts))
            . '</td>'
            . '</tr></table>';

        // Isi: satu tabel per item
        $itemsId  = $pId['items'];
        $itemsEn  = $pEn['items'];
        $maxItems = max(count($itemsId), count($itemsEn));

        for ($ii = 0; $ii < $maxItems; $ii++) {
            $iId    = isset($itemsId[$ii]) ? $itemsId[$ii] : null;
            $iEn    = isset($itemsEn[$ii]) ? $itemsEn[$ii] : null;
            $htmlId = $iId ? $renderItem($iId) : '';
            $htmlEn = $iEn ? $renderItem($iEn) : '';
            $htmlKetentuan .= '<table class="tc"><tr>'
                . '<td>' . $htmlId . '</td>'
                . '<td class="r">' . $htmlEn . '</td>'
                . '</tr></table>';
        }
    }
} else {
    foreach ($pasalKetentuan as $pi => $pasal) {
        $jId   = strtoupper(str_replace("\n", '<br>', $pasal['judul_id'] ?? ''));
        $jEn   = strtoupper(str_replace("\n", '<br>', $pasal['judul_en'] ?? ''));
        $poinId = $renderPoin($pasal['poin'] ?? [], 'id', $pasal['tipe'] ?? 'list', $rp);
        $poinEn = $renderPoin($pasal['poin'] ?? [], 'en', $pasal['tipe'] ?? 'list', $rp);

        if ($pi > 0) $htmlKetentuan .= '<div class="gap18"></div>';

        $htmlKetentuan .= '<table class="tc"><tr>'
            . '<td style="text-align:center;font-weight:bold;padding-bottom:2px;">' . $jId . '</td>'
            . '<td class="r" style="text-align:center;font-weight:bold;padding-bottom:2px;">' . $jEn . '</td>'
            . '</tr></table>';

        $htmlKetentuan .= '<table class="tc"><tr>'
            . '<td>' . $poinId . '</td>'
            . '<td class="r">' . $poinEn . '</td>'
            . '</tr></table>';
    }
}
@endphp

{!! $htmlKetentuan !!}

<div class="gap18"></div>
<table class="tc"><tr>
    <td><p>Jakarta, {{ $ttdId }}</p></td>
    <td class="r"><p>Jakarta, {{ $ttdEn }}</p></td>
</tr></table>
<div class="gap8"></div>
<table class="tc"><tr>
    <td><p>PIHAK PERTAMA/THE FIRST PARTY</p></td>
    <td class="r"><p>PIHAK KEDUA/THE SECOND PARTY</p></td>
</tr></table>
<table class="tc"><tr>
    <td><div class="sline"></div><p>{{ $kontrak->pihak_pertama }}</p></td>
    <td class="r"><div class="sline"></div><p>{{ $namaP2 }}</p></td>
</tr></table>

<div class="pgn"></div>
</body>
</html>
