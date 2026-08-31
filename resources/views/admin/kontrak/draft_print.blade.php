<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=794px, initial-scale=1">
<title>Draft Kontrak {{ $kontrak->no_kontrak }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

/*
 * Strategi font:
 * - Screen: px (1pt ≈ 1.333px di 96dpi)
 * - Print: pt langsung — browser print engine pakai ukuran fisik pt
 * Target: 13pt body agar Pasal 1 penuh hal.1, Pasal 2 mulai hal.2
 * 13pt × 1.333 ≈ 17.3px → pakai 18px untuk screen
 */

body {
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #000;
    background: #e8e8e8;
    line-height: 1.5;
}

/* ── Wrapper kertas di layar ── */
.page-wrap {
    max-width: 794px;
    margin: 20px auto 40px;
    background: #fff;
    /* 15mm ≈ 57px, 25mm ≈ 94px @96dpi */
    padding: 57px 94px 57px 94px;
    box-shadow: 0 3px 16px rgba(0,0,0,0.2);
    min-height: 1123px;
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
.doc-title .t1 { font-size: 16px; text-transform: uppercase; display: block; }
.doc-title .t2 { font-size: 15px; text-transform: uppercase; display: block; margin-top: 3px; }
.doc-title .t3 { font-size: 11px; font-weight: bold; display: block; margin-top: 2px; margin-bottom: 20px; }

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
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}
.col-l { padding-right: 18px; }
.col-r { padding-left:  18px; }

/* ── Header pasal — rata tengah kedua kolom ── */
.pasal-title { font-weight: normal; font-size: 12px; text-align: center !important; padding-top: 7px; padding-bottom: 15px; margin-bottom: 4px; line-height: 1.4; }

p { text-align: justify; margin-bottom: 4.5px; font-size: 14px; line-height: 1.4; overflow-wrap: break-word; word-break: break-word; }
strong { font-weight: bold !important; }
.sub { margin: 0 0 4.5px 16px; font-size: 13px; line-height: 1.4; font-style: italic; }

/* ── Sub-baris dalam poin (Nama Bank, No. Rek, Atas nama) rapat ── */
.poin-txt span[style*="display:table"] { line-height: 1.2 !important; margin-top: 0 !important; }

/* ── Pasal rapat (Pasal 2): antar poin tanpa jarak ── */
.pasal-rapat .poin-row { margin-bottom: 0 !important; }
.pasal-rapat p { margin-bottom: 0 !important; }
@media print {
    .pasal-rapat .poin-row { margin-bottom: 0 !important; }
    .pasal-rapat p { margin-bottom: 0 !important; }
}
.pasal9-line p { margin-bottom: 0; line-height: 1.4; }
.pasal9-line .gap8 { height: 7px; }
.pasal9-line div[style*="display:table"] { margin-bottom: 0 !important; line-height: 1.4 !important; }

/* poin bernomor */
.poin-row { display: table; width: 100%; margin-bottom: 4.5px; }
.poin-num { display: table-cell; width: 18px; vertical-align: top; white-space: nowrap; font-size: 14px; line-height: 1.4; }
.poin-txt { display: table-cell; vertical-align: top; text-align: justify; font-size: 14px; line-height: 1.4; padding-left: 3px; }
.gap4  { height: 3px;  display: block; }
.gap8  { height: 7px;  display: block; }
.gap12 { height: 11px; display: block; }
.gap20 { height: 18px; display: block; }
.gap28 { height: 1px;  display: block; }

.sline { border-top: 1px solid #000; width: 200px; margin-top: 52px; margin-bottom: 5px; }

/* ── Nomor halaman screen preview ── */
.page-num-preview {
    position: absolute;
    bottom: 8mm;
    left: 0; right: 0;
    text-align: center;
    font-size: 12px;
    color: #999;
    font-family: Arial, sans-serif;
}

/* ── PRINT ── */
/* Pakai pt di sini — browser print engine = ukuran fisik sebenarnya */
@media print {
    html, body {
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
        background: #fff !important;
        padding-top: 0 !important;
        margin: 0 !important;
        font-size: 11pt !important;
        line-height: 1.4 !important;
        font-family: Arial, sans-serif !important;
    }
    .toolbar { display: none !important; }
    .page-wrap {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        min-height: unset !important;
    }
    .two-col { width: 100% !important; table-layout: fixed !important; }
    .col-l { padding-right: 10pt !important; }
    .col-r { padding-left:  10pt !important; }
    .col-l, .col-r { width: 50% !important; overflow-wrap: break-word !important; word-break: break-word !important; }
    .page-num-preview { display: none; }
    p { font-size: 11pt !important; line-height: 1.4 !important; margin-bottom: 4.5pt !important; }
    .poin-num, .poin-txt { font-size: 11pt !important; line-height: 1.4 !important; }
    .poin-row { margin-bottom: 4.5pt !important; }
    .pasal-title { font-size: 10pt !important; line-height: 1.4 !important; padding-top: 4pt !important; padding-bottom: 6pt !important; margin-bottom: 4pt !important; }
    .sub { font-size: 10pt !important; line-height: 1.4 !important; font-style: italic !important; }
    .doc-title .t1 { font-size: 13pt !important; }
    .doc-title .t2 { font-size: 12pt !important; }
    .doc-title .t3 { font-size: 9pt !important; }
    .doc-title { margin-bottom: 14pt !important; }
    .gap4  { height: 3pt !important; }
    .gap8  { height: 7pt !important; }
    .gap12 { height: 11pt !important; }
    .gap20 { height: 7pt !important; }
    strong, b { font-weight: bold !important; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    @page {
        size: A4;
        margin: 15mm 31mm 15mm 31mm;
        @bottom-center {
            content: counter(page);
            font-family: Arial, sans-serif;
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
// Format terbilang untuk pembukaan kontrak
$tglTerbilangId = KontrakHelper::formatTanggalTerbilang($tgl, 'id');
$tglTerbilangEn = KontrakHelper::formatTanggalTerbilang($tgl, 'en');
Carbon::setLocale('id');

$dV = $kontrak->durasi_value  ?? '';
$dS = $kontrak->durasi_satuan ? ucfirst($kontrak->durasi_satuan) : '';

$mulaiId   = $kontrak->tanggal_kontrak ? Carbon::parse($kontrak->tanggal_kontrak)->isoFormat('D MMMM YYYY') : '..........';
$selesaiId = $kontrak->tanggal_selesai ? Carbon::parse($kontrak->tanggal_selesai)->isoFormat('D MMMM YYYY') : '..........';
$mulaiEn   = $kontrak->tanggal_kontrak ? Carbon::parse($kontrak->tanggal_kontrak)->locale('en')->isoFormat('D MMMM YYYY') : '..........';
$selesaiEn = $kontrak->tanggal_selesai ? Carbon::parse($kontrak->tanggal_selesai)->locale('en')->isoFormat('D MMMM YYYY') : '..........';
Carbon::setLocale('id');

$namaPerush      = $setting->nama_perusahaan    ?? 'PT. Anugerah Panca Yoga';
$namaPerushUpper = strtoupper($namaPerush);
$alamatPerush = $setting->alamat             ?? 'Jl. Catur No. 16, Menteng Dalam, Tebet, Jakarta Selatan 12870';
$telpPerush   = $setting->telepon            ?? '021 - 83792927';
$faxPerush    = $setting->fax                ?? '021 - 8354565';
$namaBank     = $setting->nama_bank          ?? 'BCA';
$noRek        = $setting->nomor_rekening     ?? '272-1420-878';
$atasNama     = strtoupper($kontrak->pihak_pertama ?? '');

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
    '{NAMA_PERUSAHAAN}'    => $namaPerushUpper,
    '{ALAMAT_PERUSAHAAN}'  => $alamatPerush,
    '{TELEPON_PERUSAHAAN}' => $telpPerush,
    '{FAX_PERUSAHAAN}'     => $faxPerush,
    '{NAMA_PIHAK_KEDUA}'         => $namaP2,
    '{NAMA_PIHAK_KEDUA_UPPER}'   => strtoupper($namaP2),
    '{ALAMAT_PIHAK_KEDUA}' => $alamatP2,
    '{KONTAK_PIHAK_KEDUA}' => $kontrak->contact_kedua ?? '…..........................',
];
$rp = function(string $t) use ($ph) {
    return str_replace(array_keys($ph), array_values($ph), $t);
};

// Juga replace jika {ATAS_NAMA} sudah terlanjur di-expand jadi nama perusahaan di DB
// Jalankan sekali lagi dengan nilai $atasNama yang benar
$rpFix = function(string $t) use ($ph) {
    // Jalankan $ph replacement
    return str_replace(array_keys($ph), array_values($ph), $t);
};

// ── Parse plain text → array pasal ──
$parsePasal = function(string $raw) use ($rp) {
    $pasals = [];
    $curNum = null;
    $cur    = null;
    foreach (explode("\n", $raw) as $line) {
        $tr = trim($line);
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
        // Baris kosong → simpan sebagai blank agar gap ter-render
        if ($tr === '') {
            $cur['items'][] = ['type' => 'blank'];
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
        // ── Special case: "Dan tidak termasuk" atau "And exclude" / "And does not include" ──
        // Treat as separate text item, not as continuation
        if (preg_match('/^Dan tidak termasuk\s*:?$/i', $tr) || 
            preg_match('/^And exclude\s*:?$/i', $tr) ||
            preg_match('/^And does not include\s*:?$/i', $tr)) {
            $cur['items'][] = ['type' => 'teks', 'text' => $rp($tr)];
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
        $cur['items'][] = ['type' => 'teks', 'text' => $rp($tr)];
    }
    if ($cur !== null) $pasals[$curNum] = $cur;
    return $pasals;
};

// ── Label Pasal 9 yang diikuti nama (tidak bold) ──
$pasal9Labels = ['PIHAK PERTAMA', 'PIHAK KEDUA', 'THE FIRST PARTY', 'THE SECOND PARTY'];

// ── Render satu item → HTML ──
$renderItem = function(array $item, bool $prevIsLabel = false) use ($pasal9Labels) {
    if ($item['type'] === 'blank') {
        return '<div class="gap8"></div>';
    }
    $raw = $item['text'] ?? '';
    $trimmed = trim($raw);
    
    // Hilangkan baris yang dimulai dengan "Hp."
    if (preg_match('/^Hp\./i', $trimmed)) {
        return '';
    }
    
    if ($item['type'] === 'poin') {
        $n = htmlspecialchars($item['num'] ?? '');
        // Render multi-line: baris pertama lanjut, baris berikutnya indent sejajar
        $lines = explode("\n", $raw);
        $first = str_replace('/', '/&#8203;', htmlspecialchars(array_shift($lines)));
        $rest  = '';
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln === '') continue;
            // Format "Label : Nilai" → rata kolom seperti tabel, plain (no bold)
            if (preg_match('/^(.+?)\s*:\s*(.+)$/', $ln, $lm)) {
                $rest .= '<span style="display:table;width:100%;line-height:1.3;">'
                       . '<span style="display:table-cell;white-space:nowrap;vertical-align:top;width:75pt;">' . htmlspecialchars($lm[1]) . '</span>'
                       . '<span style="display:table-cell;white-space:nowrap;vertical-align:top;width:8pt;padding-right:4pt;">:</span>'
                       . '<span style="display:table-cell;vertical-align:top;word-break:break-word;overflow-wrap:break-word;font-weight:bold;">' . htmlspecialchars($lm[2]) . '</span>'
                       . '</span>';
            } else {
                // Insert zero-width space after "/" to allow wrap
                $rest .= '<span style="display:block;line-height:1.3;">' . str_replace('/', '/&#8203;', htmlspecialchars($ln)) . '</span>';
            }
        }
        return '<div class="poin-row"><span class="poin-num">' . $n . '.</span><span class="poin-txt">' . $first . $rest . '</span></div>';
    }
    
    if ($item['type'] === 'sub') {
        // ── Special case: "Dan tidak termasuk" atau "And exclude" / "And does not include" ──
        if (preg_match('/^Dan tidak termasuk\s*:?$/i', $trimmed) || 
            preg_match('/^And exclude\s*:?$/i', $trimmed) ||
            preg_match('/^And does not include\s*:?$/i', $trimmed)) {
            return '<p style="margin:10px 0 4px 14px;font-weight:normal;">' . htmlspecialchars($raw) . '</p>';
        }
        // Regular sub-item — insert zero-width space after "/" to allow wrap
        $safe = str_replace('/', '/&#8203;', htmlspecialchars($raw));
        return '<div style="display:table;width:100%;margin:2px 0 2px 14px;">'
             . '<span style="display:table-cell;width:10px;vertical-align:top;white-space:nowrap;font-size:13px;line-height:1.5;font-style:italic;">-</span>'
             . '<span style="display:table-cell;vertical-align:top;text-align:justify;padding-left:4px;font-size:13px;line-height:1.5;font-style:italic;">' . $safe . '</span>'
             . '</div>';
    }
    
    // ── Handle type 'teks' ──
    // Special case: "Dan tidak termasuk" atau "And exclude" / "And does not include"
    if (preg_match('/^Dan tidak termasuk\s*:?$/i', $trimmed) || 
        preg_match('/^And exclude\s*:?$/i', $trimmed) ||
        preg_match('/^And does not include\s*:?$/i', $trimmed)) {
        return '<p style="margin:10px 0 4px 0;font-weight:normal;">' . htmlspecialchars($raw) . '</p>';
    }
    // teks all-caps → plain, tidak ada yang bold
    if (strtoupper($raw) === $raw && strlen($trimmed) > 3) {
        // Label PIHAK KEDUA / THE SECOND PARTY → tambah margin atas sebagai pemisah blok
        if (preg_match('/^(PIHAK\s+KEDUA|THE\s+SECOND\s+PARTY)$/i', $trimmed)) {
            return '<p style="margin-top:12pt;">' . htmlspecialchars($raw) . '</p>';
        }
        // Label PIHAK PERTAMA / THE FIRST PARTY → tidak ada margin atas (sudah ada dari container)
        if (preg_match('/^(PIHAK\s+PERTAMA|THE\s+FIRST\s+PARTY)$/i', $trimmed)) {
            return '<p style="margin-top:8pt;">' . htmlspecialchars($raw) . '</p>';
        }
        return '<p>' . htmlspecialchars($raw) . '</p>';
    }
    // format "Label : Nilai" → rata kolom, plain (no bold, no min-width)
    if (preg_match('/^(.+?)\s*:\s*(.+)$/', $trimmed, $m)) {
        return '<div style="display:table;width:100%;margin:0;padding:0;">'
             . '<span style="display:table-cell;white-space:nowrap;vertical-align:top;width:75pt;line-height:1.6;">' . htmlspecialchars($m[1]) . '</span>'
             . '<span style="display:table-cell;white-space:nowrap;vertical-align:top;width:8pt;padding-right:4pt;line-height:1.6;">:</span>'
             . '<span style="display:table-cell;vertical-align:top;word-break:break-word;overflow-wrap:break-word;line-height:1.6;font-weight:bold;">' . htmlspecialchars($m[2]) . '</span>'
             . '</div>';
    }
    // Insert zero-width space after "/" to allow word wrap
    return '<p>' . str_replace('/', '/&#8203;', htmlspecialchars($raw)) . '</p>';
};

// ── Render format lama (pasal JSON) ──
$renderPoin = function(array $poinArr, string $lang, string $tipe, callable $rp) {
    $out = '';
    if ($tipe === 'paragraf') {
        foreach ($poinArr as $p) {
            $lines = explode("\n", $rp($p[$lang] ?? ''));
            $prevBlank = false;
            foreach ($lines as $idx => $line) {
                $tr = trim($line);
                if ($tr === '') {
                    $prevBlank = true;
                    continue;
                }
                // Jika baris sebelumnya kosong & teks ini all-caps (seperti PIHAK PERTAMA/THE FIRST PARTY),
                // tambahkan margin top lebih besar
                $isAllCaps = (strtoupper($tr) === $tr && strlen($tr) > 3);
                if ($prevBlank && $isAllCaps) {
                    $out .= '<p style="margin-top:8px;">' . htmlspecialchars($tr) . '</p>';
                } elseif ($prevBlank) {
                    $out .= '<p style="margin-top:8px;">' . htmlspecialchars($tr) . '</p>';
                } elseif ($idx > 0 && preg_match('/^-\s+(.+)/u', $tr, $m)) {
                    $out .= '<p class="sub">- ' . htmlspecialchars($m[1]) . '</p>';
                } elseif ($idx > 0 && preg_match('/^[A-Za-z]\.\s/', $tr)) {
                    $out .= '<p class="sub">' . htmlspecialchars($tr) . '</p>';
                } else {
                    $out .= '<p>' . htmlspecialchars($tr) . '</p>';
                }
                $prevBlank = false;
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
                $out .= '<br><span style="display:inline-block;padding-left:12px;font-size:13px;">'
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

        // Deteksi apakah ini Pasal 9 (PEMBERITAHUAN / NOTICE)
        $isPasal9 = str_contains(strtoupper(implode(' ', $judulIdParts)), 'PEMBERITAHUAN')
                 || str_contains(strtoupper(implode(' ', $judulEnParts)), 'NOTICE');

        // Deteksi Pasal 2 (MASA SEWA / RENTAL PERIOD) — antar poin rapat
        $isPasal2 = ($num === 2)
                 || str_contains(strtoupper(implode(' ', $judulIdParts)), 'MASA SEWA')
                 || str_contains(strtoupper(implode(' ', $judulEnParts)), 'RENTAL PERIOD');

        $wrapOpen  = $isPasal9 ? '<div class="pasal9-line">' : ($isPasal2 ? '<div class="pasal-rapat">' : '');
        $wrapClose = ($isPasal9 || $isPasal2) ? '</div>' : '';

        // Judul
        $htmlKetentuan .= '<div class="two-col">'
            . '<div class="col-l pasal-title">' . implode('<br>', array_map('htmlspecialchars', $judulIdParts)) . '</div>'
            . '<div class="col-r pasal-title">' . implode('<br>', array_map('htmlspecialchars', $judulEnParts)) . '</div>'
            . '</div>'
            . $wrapOpen;

        // ── Render per-poin berpasangan agar nomor ID sejajar dengan EN ──
        // Kelompokkan items per poin bernomor (beserta sub-items yang mengikutinya)
        // Untuk Pasal 9: kelompokkan per blok (dipisah blank) agar PIHAK PERTAMA+detail stay together
        $groupItems = function(array $items) use ($isPasal9) {
            $groups = [];
            $cur    = null;
            if ($isPasal9) {
                // Pasal 9: grup 1 = kalimat pembuka (sebelum PIHAK PERTAMA)
                //           grup 2 = PIHAK PERTAMA + detail + PIHAK KEDUA + detail (semua jadi 1 grup)
                $intro = [];
                $body  = [];
                $foundPihak = false;
                foreach ($items as $item) {
                    $txt = trim($item['text'] ?? '');
                    if (!$foundPihak && $item['type'] === 'teks'
                        && preg_match('/^(PIHAK\s+PERTAMA|THE\s+FIRST\s+PARTY)$/i', $txt)) {
                        $foundPihak = true;
                    }
                    if ($foundPihak) {
                        if ($item['type'] !== 'blank') $body[] = $item;
                        // blank di tengah body diabaikan agar tetap 1 grup
                    } else {
                        if ($item['type'] !== 'blank') $intro[] = $item;
                    }
                }
                if (!empty($intro)) $groups[] = $intro;
                if (!empty($body))  $groups[] = $body;
            } else {
                foreach ($items as $item) {
                    if ($item['type'] === 'poin') {
                        if ($cur !== null) $groups[] = $cur;
                        $cur = [$item];
                    } elseif ($cur !== null) {
                        $cur[] = $item;
                    } else {
                        $groups[] = [$item];
                    }
                }
                if ($cur !== null) $groups[] = $cur;
            }
            return $groups;
        };

        $groupsId = $groupItems($pId['items']);
        $groupsEn = $groupItems($pEn['items']);
        $maxGroups = max(count($groupsId), count($groupsEn));

        for ($gi = 0; $gi < $maxGroups; $gi++) {
            $gId = $groupsId[$gi] ?? [];
            $gEn = $groupsEn[$gi] ?? [];

            $htmlId = '';
            $prevIsLabel = false;
            foreach ($gId as $item) {
                $html = $renderItem($item, $prevIsLabel);
                $htmlId .= $html;
                $prevIsLabel = ($item['type'] === 'teks' && in_array(trim($item['text'] ?? ''), $pasal9Labels));
            }

            $htmlEn = '';
            $prevIsLabel = false;
            foreach ($gEn as $item) {
                $html = $renderItem($item, $prevIsLabel);
                $htmlEn .= $html;
                $prevIsLabel = ($item['type'] === 'teks' && in_array(trim($item['text'] ?? ''), $pasal9Labels));
            }

            // Untuk Pasal 9: grup body (PIHAK PERTAMA+KEDUA) dibungkus avoid
            $isAddrGroup = false;
            $isPihakKedua = false;
            if ($isPasal9) {
                foreach (array_merge($gId, $gEn) as $item) {
                    $txt = trim($item['text'] ?? '');
                    if ($item['type'] === 'teks' && preg_match('/^(PIHAK\s+PERTAMA|THE\s+FIRST\s+PARTY)$/i', $txt)) {
                        $isAddrGroup = true; break;
                    }
                }
            }

            if ($isAddrGroup) {
                $htmlKetentuan .= '<div style="page-break-inside:avoid;break-inside:avoid;">'
                    . '<div class="two-col" style="margin-bottom:0;">'
                    . '<div class="col-l">' . $htmlId . '</div>'
                    . '<div class="col-r">' . $htmlEn . '</div>'
                    . '</div></div>';
            } else {
                $htmlKetentuan .= '<div class="two-col" style="margin-bottom:0;">'
                    . '<div class="col-l">' . $htmlId . '</div>'
                    . '<div class="col-r">' . $htmlEn . '</div>'
                    . '</div>';
            }
        }
        $htmlKetentuan .= $wrapClose;
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
        <div class="col-l"><p>Pada hari ini, {{ $tglTerbilangId }}, kami yang bertanda tangan dibawah ini, masing-masing:</p></div>
        <div class="col-r"><p>On this day, {{ $tglTerbilangEn }}, we the undersigned, respectively:</p></div>
    </div>
    <div class="gap12"></div>

    <div class="two-col">
        <div class="col-l"><p><strong>1. {{ $namaPerush }},</strong> suatu perseroan terbatas yang memiliki kantor terdaftar di {{ $alamatPerush }}, dalam hal ini diwakili oleh {{ $kontrak->pihak_pertama }}, jabatan {{ $pihak1Jabatan }}, pemegang KTP No. {{ $pihak1KTP }}, selanjutnya disebut "Pihak Pertama"; dan</p></div>
        <div class="col-r"><p><strong>1. {{ $namaPerush }},</strong> a limited liability company, having its registered office at {{ $alamatPerush }}, in this matter represented by {{ $kontrak->pihak_pertama }}, acting as {{ $pihak1Jabatan }}, a holder of identity card no. {{ $pihak1KTP }}, hereinafter referred to as the "First Party"; and</p></div>
    </div>
    <div class="gap12"></div>

    @php
    $isPihak2Perusahaan = strtolower($kontrak->jenis_pelanggan ?? 'perorangan') === 'perusahaan';
    $perwakilan2 = $kontrak->perwakilan_pihak_kedua ?? null;
    $jabatan2    = $kontrak->jabatan_pihak_kedua ?? null;
    @endphp

    <div class="two-col">
        @if($isPihak2Perusahaan && $perwakilan2 && $jabatan2)
            {{-- Format lengkap perusahaan dengan perwakilan --}}
            <div class="col-l"><p><strong>2. {{ $namaP2 }},</strong> suatu perseroan terbatas yang memiliki kantor terdaftar di {{ $alamatP2 }}, dalam hal ini diwakili oleh <strong>{{ $perwakilan2 }}</strong> dalam jabatannya selaku {{ $jabatan2 }}, pemegang KTP No. {{ $noktp2 }}, selanjutnya disebut sebagai "Pihak Kedua".</p></div>
            <div class="col-r"><p><strong>2. {{ $namaP2 }},</strong> a limited liability company having its registered office at {{ $alamatP2 }}, in this matter represented by <strong>{{ $perwakilan2 }}</strong>, a holder of passport no. or Kitas no. {{ $noktp2 }}, acting as {{ $jabatan2 }}, hereinafter referred to as the "Second Party".</p></div>
        @elseif($isPihak2Perusahaan)
            {{-- Format perusahaan tanpa perwakilan (skip "diwakili oleh") --}}
            <div class="col-l"><p><strong>2. {{ $namaP2 }},</strong> suatu perseroan terbatas yang memiliki kantor terdaftar di {{ $alamatP2 }}, pemegang KTP No. {{ $noktp2 }}, selanjutnya disebut sebagai "Pihak Kedua".</p></div>
            <div class="col-r"><p><strong>2. {{ $namaP2 }},</strong> a limited liability company, having its registered office at {{ $alamatP2 }}, holder of identity card no. {{ $noktp2 }}, hereinafter referred to as the "Second Party".</p></div>
        @else
            {{-- Format perorangan (existing) --}}
            <div class="col-l"><p><strong>2. {{ $namaP2 }},</strong> yang beralamat di {{ $alamatP2 }}, pemegang KTP No. {{ $noktp2 }}, selanjutnya disebut sebagai "Pihak Kedua".</p></div>
            <div class="col-r"><p><strong>2. {{ $namaP2 }},</strong> domiciled at {{ $alamatP2 }}, holder of identity card no. {{ $noktp2 }}, hereinafter referred to as the "Second Party".</p></div>
        @endif
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
    {{-- ── GRUP TANDA TANGAN (tidak terpotong halaman) ── --}}
    <div style="page-break-inside: avoid; break-inside: avoid;">

        <div class="two-col">
            <div class="col-l"><p>Jakarta, {{ $ttdId }}</p></div>
            <div class="col-r"><p>Jakarta, {{ $ttdEn }}</p></div>
        </div>
        <div class="gap8"></div>
        <div class="two-col">
            <div class="col-l"><p style="margin-top:4px;font-size:16px;">PIHAK PERTAMA/THE FIRST PARTY</p></div>
            <div class="col-r"><p style="margin-top:4px;font-size:16px;">PIHAK KEDUA/THE SECOND PARTY</p></div>
        </div>
        <div class="two-col">
            <div class="col-l"><div class="sline" style="margin-top:70px;"></div><p>{{ $kontrak->pihak_pertama }}</p></div>
            <div class="col-r"><div class="sline" style="margin-top:70px;"></div><p>{{ $perwakilan2 ?? $namaP2 }}</p></div>
        </div>

    </div>{{-- /grup-tanda-tangan --}}

</div>{{-- /page-wrap --}}

</body>
</html>
