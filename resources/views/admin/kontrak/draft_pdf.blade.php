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

@page { margin: 0mm; }
</style>
</head>
<body>
@php
    use Carbon\Carbon;
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

    // Data Pihak Kedua — cari dari tabel member dengan prioritas customer_name penawaran
    // karena data member disimpan berdasarkan customer_name saat input penawaran
    $namaP2 = $kontrak->penawaran->customer_name
               ?? $kontrak->penawaran->kepada
               ?? $kontrak->pihak_kedua;

    // Cari member: coba customer_name dulu, lalu kepada, lalu pihak_kedua
    $pelangganP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->penawaran->customer_name ?? '')
                    ->first();
    if (!$pelangganP2 && $kontrak->penawaran?->kepada) {
        $pelangganP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->penawaran->kepada)->first();
    }
    if (!$pelangganP2) {
        $pelangganP2 = \App\Models\Pelanggan::where('nama_pelanggan', $kontrak->pihak_kedua)->first();
    }

    $alamatP2 = $pelangganP2->alamat  ?? '…………………………………………………………………………';
    $noktp2   = $pelangganP2->no_ktp  ?? '……………………………………';

    // PPN & PPH dari setting
    $ppn = $setting->ppn_default ?? 11;
    $pph = $setting->pph_default ?? 2;
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

{{-- ════════════════════ HALAMAN 2 ════════════════════ --}}
<div class="page" style="padding: 16mm 20mm 14mm 20mm;">
<table class="tc" style="font-size:10.5pt; line-height:1.38;">

<tr>
    <td><div class="ptitle">PASAL 2<br>MASA SEWA</div></td>
    <td class="r"><div class="ptitle">ARTICLE 2<br>RENTAL PERIOD</div></td>
</tr>
<tr>
    <td>
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">Mobil tersebut diatas disewa oleh PIHAK KEDUA untuk jangka waktu
                <strong>{{ $dV }} {{ $dS }}</strong>, mulai {{ $mulaiId }} s/d {{ $selesaiId }},
                terhitung sejak tanggal serah terima kendaraan.</span></li>
            <li><span class="nb">2.</span><span class="tx">Apabila kendaraan tidak dikembalikan tepat waktu, maka akan dikenakan
                biaya sewa harian sebesar Rp. 400.000,- / hari.</span></li>
            <li><span class="nb">3.</span><span class="tx">Pengiriman kendaraan paling lambat 3 minggu setelah diterimanya SPK
                (Surat Perintah Kerja) atau PO (Purchase Order).</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">The Car mentioned above shall be rented by the Second Party for a period
                of <strong>{{ $dV ? $dV.' '.$dS : '………… - …………' }}</strong>, commencing
                {{ $mulaiEn }} until {{ $selesaiEn }},
                after the delivery of cars.</span></li>
            <li><span class="nb">2.</span><span class="tx">If the vehicle is not returned on time, it will be charged a daily rental
                of Rp.400.000,- / day</span></li>
            <li><span class="nb">3.</span><span class="tx">Delivery of cars at least 3 (three) weeks after the receipt of Work
                Authorization or PO (Purchase Order)</span></li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 3<br>HARGA SEWA DAN PEMBAYARAN</div></td>
    <td class="r"><div class="ptitle">ARTICLE 3<br>RENTAL PRICE AND PAYMENT</div></td>
</tr>
<tr>
    <td>
        <ul class="lst" style="margin:2px 0 4px 0;">
            <li style="margin-bottom:3px;"><span class="nb">1.</span><span class="tx">Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">2.</span><span class="tx">Sewa mobil yang dibayarkan sudah termasuk :<br>
                &nbsp;&nbsp;-Pemeliharaan dan reparasi Kendaraan<br>
                &nbsp;&nbsp;- Biaya STNK/KIR<br>
                &nbsp;&nbsp;- Asuransi All Risk<br>
                &nbsp;&nbsp;- Kendaraan Pengganti<br>
                <em>Dan tidak termasuk</em><br>
                &nbsp;&nbsp;- PPN {{ $ppn }}%<br>
                &nbsp;&nbsp;- PPH 23 {{ $pph }}%<br>
                &nbsp;&nbsp;- Bensin, parkir dan tol.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">3.</span><span class="tx">PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada
                PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi
                pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang
                mendukung.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">4.</span><span class="tx">Pembayaran dilakukan melalui:<br>
                &nbsp;&nbsp;Nama Bank &nbsp;&nbsp;: <strong>{{ $namaBank }}</strong><br>
                &nbsp;&nbsp;No. Rekening : <strong>{{ $noRek }}</strong><br>
                &nbsp;&nbsp;Atas nama &nbsp;&nbsp;: <strong>{{ $atasNama }}</strong></span></li>
            <li style="margin-bottom:3px;"><span class="nb">5.</span><span class="tx">Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana
                disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai
                sewa per hari untuk setiap hari keterlambatan.</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst" style="margin:2px 0 4px 0;">
            <li style="margin-bottom:3px;"><span class="nb"><em>1.</em></span><span class="tx"><em>The car rental fee is written on the attachment 1 (one) of the Agreement.</em></span></li>
            <li style="margin-bottom:3px;"><span class="nb">2.</span><span class="tx"><em>The car rental fee paid include:</em><br>
                &nbsp;&nbsp;- Maintenance And Repair<br>
                &nbsp;&nbsp;-Motor Vehicle Document (STNK/KIR)<br>
                &nbsp;&nbsp;- All Risk Insurance<br>
                &nbsp;&nbsp;-Replacement car<br>
                <em>And exclude</em><br>
                &nbsp;&nbsp;- Value Added Tax {{ $ppn }}%<br>
                &nbsp;&nbsp;- Income tax {{ $pph }}%<br>
                &nbsp;&nbsp;- Gasoline, parking and toll fee</span></li>
            <li style="margin-bottom:3px;"><span class="nb">3.</span><span class="tx">The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental
                fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the
                current month, along with attach invoice, VAT certificate and other supporting
                document.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">4.</span><span class="tx">Payment &nbsp; is &nbsp; done &nbsp; through:<br>
                &nbsp;&nbsp;BankName &nbsp;&nbsp;&nbsp;:<strong>{{ $namaBank }}</strong><br>
                &nbsp;&nbsp;Account No. : <strong>{{ $noRek }}</strong><br>
                &nbsp;&nbsp;Account Name: <strong>{{ $atasNama }}</strong></span></li>
            <li style="margin-bottom:3px;"><span class="nb">5.</span><span class="tx">If The SECOND PARTY fails to perform the payment obligation mentioned
                above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total
                payable rent per day.</span></li>
        </ul>
    </td>
</tr>

</table>

</div>

{{-- ════════════════════ HALAMAN 3 ════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td><div class="ptitle">PASAL 4<br>KEWAJIBAN PIHAK PERTAMA</div></td>
    <td class="r"><div class="ptitle">ARTICLE 4<br>OBLIGATION OF THE FIRST PARTY</div></td>
</tr>
<tr>
    <td>
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan
                di pool/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK
                PERTAMA sehingga mobil dalam keadaan siap beroperasi/baik selama masa sewa.</span></li>
            <li><span class="nb">2.</span><span class="tx">Batas jarak tempuh kendaraan adalah sebesar 2500 km/bulan</span></li>
            <li><span class="nb">3.</span><span class="tx">Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.</span></li>
            <li><span class="nb">4.</span><span class="tx">Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada
                di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.</span></li>
            <li><span class="nb">5.</span><span class="tx">PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil
                yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh
                empat jam) jam.</span></li>
            <li><span class="nb">6.</span><span class="tx">PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu
                mencapai pemakaian 60.000 km atau setelah 2 tahun.</span></li>
            <li><span class="nb">7.</span><span class="tx">PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk
                tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:
                <ul class="alst">
                    <li><span class="nb">a.</span><span class="tx">Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan
                        polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan
                        dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab
                        PIHAK KEDUA.</span></li>
                    <li><span class="nb">b.</span><span class="tx">Dalam hal kecelakaan/kehilangan/pencurian mobil yang disewa, dimana
                        kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih
                        menjadi tanggung jawab PIHAK KEDUA.</span></li>
                    <li><span class="nb">c.</span><span class="tx">Selama proses pengurusan</span></li>
                </ul>
            </span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">The FIRST PARTY is obligated to maintain and repair the rented car in
                the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST
                PARTY so that the car is in good condition during the rental period.</span></li>
            <li><span class="nb">2.</span><span class="tx">The maximum distance travel in a month is 2500 km</span></li>
            <li><span class="nb">3.</span><span class="tx">The mileage can be accumulated and the surcharge will be billed in the end
                of the rent period.</span></li>
            <li><span class="nb">4.</span><span class="tx">If the car breakdown when it is out of town due to engine failure, additional
                costs will be charged for on call services.</span></li>
            <li><span class="nb">5.</span><span class="tx">FIRST PARTY has agreed to provide replacement car in case of the car rent
                by SECOND PARTY is being repaired for more than 24 (twenty four) hours.</span></li>
            <li><span class="nb">6.</span><span class="tx">THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use
                first or after 2 years.</span></li>
            <li><span class="nb">7.</span><span class="tx">The FIRST PARTY is obligated to insure the rented car with all risk insurance
                but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following
                provisions:
                <ul class="alst">
                    <li><span class="nb">a.</span><span class="tx">Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or
                        maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per
                        occurrence. Exceeding amount becomes the SECOND PARTY responsibility.</span></li>
                    <li><span class="nb">b.</span><span class="tx">In the event of damage/loss/theft of the car, hence the claim is rejected
                        by the insurance company and in effect will hold responsible fully to the
                        cost effect of occurrence.</span></li>
                    <li><span class="nb">c.</span><span class="tx">While undergoing the process of</span></li>
                </ul>
            </span></li>
        </ul>
    </td>
</tr>

</table>

</div>

{{-- ════════════════════ HALAMAN 4 ════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td>
        <ul class="alst" style="margin-left:22px;">
            <li><span class="nb">c.</span><span class="tx">pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak
                mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar
                10% dari uang pertanggungan yang tertera di polis.</span></li>
            <li><span class="nb">d.</span><span class="tx">Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair,
                PIHAK KEDUA berkewajiban membayar biaya resiko sendiri</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="alst" style="margin-left:22px;">
            <li><span class="nb">c.</span><span class="tx">insurance claim for the loss/theft of the car, The SECOND PARTY will not
                receive replacement car and responsible to pay own risk claim of 10% of the
                insured sum that is written in the insurance policy.</span></li>
            <li><span class="nb">d.</span><span class="tx">In the event of accident that requires body repair, the SECOND PARTY is
                obligated to pay own risk</span></li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s12"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 5<br>KEWAJIBAN PIHAK KEDUA</div></td>
    <td class="r"><div class="ptitle">PASAL 5<br>OBLIGATION OF THE SECOND PARTY</div></td>
</tr>
<tr>
    <td>
        <ul class="lst">
            <li><span class="nb">1</span><span class="tx">PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta
                menyediakan tempat parkir yang aman.</span></li>
            <li><span class="nb">2.</span><span class="tx">Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.</span></li>
            <li><span class="nb">3.</span><span class="tx">Bila terjadi kehilangan/pencurian mobil, PIHAK KEDUA berkewajiban untuk
                memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan
                kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat
                Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang
                dikeluarkan menjadi tanggung jawab PIHAK KEDUA.</span></li>
            <li><span class="nb">4.</span><span class="tx">PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil
                tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan</span></li>
            <li><span class="nb">5.</span><span class="tx">PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil,
                menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.</span></li>
            <li><span class="nb">6.</span><span class="tx">PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK
                PERTAMA dalam hal:
                <ul class="alst">
                    <li><span class="nb">a.</span><span class="tx">Perubahan nama/alamat PIHAK KEDUA</span></li>
                </ul>
            </span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">SECOND PARTY assures to keep and protect the rented car and also provide
                a safe parking lot.</span></li>
            <li><span class="nb">2.</span><span class="tx">During rental period, the car will be parked in SECOND PARTY's parking lot.</span></li>
            <li><span class="nb">3.</span><span class="tx">In the event of loss/theft, SECOND PARTY has obligation to inform FIRST PARTY
                within 24 hour. Together, both parties report to the Police station in order to
                obtain the Lost Report Information Letter and Vehicle Motor Document (STNK)
                Blocking Letter that is issued by Police Department (POLDA). All costs incurred
                will be responsibility of SECOND PARTY.</span></li>
            <li><span class="nb">4.</span><span class="tx">The SECOND PARTY is not allowed to re-let and/or transfer its right in any
                nature to any other party including making the car as a guarantee.</span></li>
            <li><span class="nb">5.</span><span class="tx">The SECOND PARTY is not allowed to change and/or replace the form of the
                car, to add, replace, or detach any part of the car without previously notify
                the FIRST PARTY.</span></li>
            <li><span class="nb">6.</span><span class="tx">The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :
                <ul class="alst">
                    <li><span class="nb">a.</span><span class="tx">If the SECOND PARTY intends to</span></li>
                </ul>
            </span></li>
        </ul>
    </td>
</tr>

</table>

</div>

{{-- ════════════════════ HALAMAN 5 ════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td>
        <ul class="alst" style="margin-left:22px;">
            <li><span class="nb">b.</span><span class="tx">Jika ada perubahan dalam fungsi atau kegunaan mobil.</span></li>
        </ul>
        <ul class="lst" style="margin-top:4px;">
            <li><span class="nb">7.</span><span class="tx">PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk
                balap/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan
                alasan komersial atau alasan lainnya selain alasan domestik atau sosial.</span></li>
            <li><span class="nb">8.</span><span class="tx">Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula,
                dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya
                waktu.</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="alst" style="margin-left:22px;">
            <li><span class="nb">b.</span><span class="tx">In case of any change of car utilization purpose.</span></li>
        </ul>
        <ul class="lst" style="margin-top:4px;">
            <li><span class="nb">7.</span><span class="tx">The SECOND PARTY is not allowed to use the car in/for any car race,
                political campaign, criminal action, carrying any passenger for commercial
                purpose and/or any other purpose besides the domestic and social purposes.</span></li>
            <li><span class="nb">8.</span><span class="tx">To return the car on the expiration of the lease duration in original
                condition, save for reasonable wear and tear due the passage of time.</span></li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 6<br>STNK</div></td>
    <td class="r"><div class="ptitle">ARTICLE 6<br>MOTOR VEHICLE DOCUMENT (STNK)</div></td>
</tr>
<tr>
    <td>
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan
                dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.</span></li>
            <li><span class="nb">2.</span><span class="tx">PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul
                sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan
                perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">The extension cost of the Motor Vehicle Document (STNK) shall be paid by
                the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration
                date.</span></li>
            <li><span class="nb">2.</span><span class="tx">The SECOND PARTY is responsible for all the costs born for the lost of
                vehicle legal document (STNK) and also for any delay in extension process
                because of the SECOND PARTY negligence.</span></li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 7<br>PENGEMUDI</div></td>
    <td class="r"><div class="ptitle">ARTICLE 7<br>DRIVER</div></td>
</tr>
<tr>
    <td>
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.</span></li>
            <li><span class="nb">2.</span><span class="tx">PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang
                ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK
                KEDUA dan tidak di parkir di tempat yang telah ditentukan.</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst">
            <li><span class="nb">1.</span><span class="tx">The Car lease shall be driven by SECOND PARTY' s driver</span></li>
            <li><span class="nb">2.</span><span class="tx">SECOND PARTY shall bear all losses and legal consequences caused when the
                vehicle is being driven by a driver who was assigned by the SECOND PARTY and
                when the vehicle is not parked in the designated parking lot.</span></li>
        </ul>
    </td>
</tr>

</table>

</div>

{{-- ════════════════════ HALAMAN 6 ════════════════════ --}}
<div class="page" style="padding: 16mm 20mm 14mm 20mm;">
<table class="tc" style="font-size:10.5pt; line-height:1.38;">

<tr>
    <td><div class="ptitle">PASAL 8<br>PEMUTUSAN DAN PERPANJANGAN<br>PERJANJIAN</div></td>
    <td class="r"><div class="ptitle">ARTICLE 8<br>TERMINATION AND EXTENSION OF<br>AGREEMENT</div></td>
</tr>
<tr>
    <td>
        <ul class="lst" style="margin:2px 0 4px 0;">
            <li style="margin-bottom:3px;"><span class="nb">1.</span><span class="tx">Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal
                pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian
                ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim
                surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA
                dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat
                peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK
                PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA
                menyerahkan kendaraan kepada PIHAK KEDUA.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">2.</span><span class="tx">Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah
                kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu
                kesatuan yang tidak terpisahkan dengan Perjanjian ini.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">3.</span><span class="tx">Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum
                masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh
                persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak
                dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama
                digunakan</span></li>
            <li style="margin-bottom:3px;"><span class="nb">4.</span><span class="tx">Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada
                lampiran perjanjian ini.</span></li>
        </ul>
    </td>
    <td class="r">
        <ul class="lst" style="margin:2px 0 4px 0;">
            <li style="margin-bottom:3px;"><span class="nb">1.</span><span class="tx">If the SECOND PARTY fails to fulfill one of the stipulations with respect to
                the payment obligation to the FIRST PARTY as regulated in this agreement, the
                FIRST PARTY shall have the right to terminate this agreement by delivering a
                written reprimand 3 (three) calendar days before the termination to the SECOND
                PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives
                the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the
                car to the FIRST PARTY in a good condition and at the location where the FIRST
                PARTY hand over the car to the SECOND PARTY at the first time.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">2.</span><span class="tx">Both parties can extend the contract period and or add the quantity of cars
                rented with a supplemental agreement (addendum), which is an inseparable part
                of the Agreement.</span></li>
            <li style="margin-bottom:3px;"><span class="nb">3.</span><span class="tx">Both PARTIES can terminate/cancel this Agreement before the expiry date but
                liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent
                and the rental fee that has been paid upfront could not be refunded, unless the
                car is often damaged during use</span></li>
            <li style="margin-bottom:3px;"><span class="nb">4.</span><span class="tx">All other matter which is not covered in the articles will be covered in
                the attachment.</span></li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s4"></span></td></tr>

<tr>
    <td><div class="ptitle">ARTICLE 9<br>PEMBERITAHUAN</div></td>
    <td class="r"><div class="ptitle">ARTICLE 9<br>NOTICE</div></td>
</tr>
<tr>
    <td>
        <p>Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan
        perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau
        dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:</p>
    </td>
    <td class="r">
        <p>Any notice, request and other communications relating to this agreement must be
        made in writing and submitted in person or delivered through courier or facsimile
        to parties in the following addresses:</p>
    </td>
</tr>

</table>

</div>

{{-- ════════════════════ HALAMAN 7 ════════════════════ --}}
<div class="page" style="padding: 16mm 20mm 14mm 20mm;">
<table class="tc" style="font-size:10.5pt; line-height:1.38;">

{{-- Lanjutan Pasal 9 — Alamat Pihak --}}
<tr>
    <td>
        <p><strong>PIHAK PERTAMA</strong><br>
        {{ strtoupper($namaPerush) }}<br>
        {{ $alamatPerush }}<br>
        Telp. &nbsp;{{ $telpPerush }}<br>
        Fax. &nbsp; {{ $faxPerush }}</p>
        <span class="s8"></span>
        <p><strong>PIHAK KEDUA</strong><br>
        ………………………………<br>
        ………………………………<br>
        ………………………………<br>
        ………………………………<br>
        Hp. .............................</p>
    </td>
    <td class="r">
        <p><strong>THE FIRST PARTY</strong><br>
        {{ strtoupper($namaPerush) }}<br>
        {{ $alamatPerush }}<br>
        Telp. &nbsp;{{ $telpPerush }}<br>
        Fax. &nbsp; {{ $faxPerush }}</p>
        <span class="s8"></span>
        <p><strong>THE SECOND PARTY</strong><br>
        ………………………………<br>
        ………………………………<br>
        ………………………………<br>
        ………………………………<br>
        Hp. .............................</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

{{-- PASAL 10 --}}
<tr>
    <td><div class="ptitle">ARTICLE 10<br>PENUTUP</div></td>
    <td class="r"><div class="ptitle">ARTICLE 10<br>CLOSING PROVISION</div></td>
</tr>
<tr>
    <td>
        <p>Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara
        musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka
        kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di
        Kantor Panitera Negeri Jakarta Selatan, di Jakarta.</p>
        <span class="s8"></span>
        <p>Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh
        kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan
        mempunyai kekuatan hukum yang sama.</p>
    </td>
    <td class="r">
        <p>Any dispute arising form this Agreement of Vehicle Rent shall be settled in
        deliberation by Both Parties, and if the dispute cannot be settled in deliberation,
        Both Parties shall agree to elect the general and permanent domicile at the office
        clerk of the District Court of Jakarta Selatan, in Jakarta.</p>
        <span class="s8"></span>
        <p>In witness whereof this Agreement of Vehicle Rent was made and signed by Both
        Parties in duplicate, each duty stamped and having the same legal force.</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

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
