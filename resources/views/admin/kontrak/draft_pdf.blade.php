<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Perjanjian Sewa Menyewa - {{ $kontrak->no_kontrak }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: Arial, sans-serif;
    font-size: 10.5pt;
    color: #000;
    background: #fff;
    line-height: 12.5pt;
}

.page {
    padding: 55px 60px 40px 65px;
    page-break-after: always;
}
.page:last-child {
    page-break-after: avoid;
}

/* ── JUDUL ── */
.doc-title {
    text-align: center;
    margin-bottom: 20px;
}
.doc-title .t1 {
    font-size: 13pt;
    font-weight: bold;
    text-transform: uppercase;
}
.doc-title .t2 {
    font-size: 12pt;
    font-weight: bold;
    text-transform: uppercase;
}
.doc-title .t3 {
    font-size: 10.5pt;
    font-weight: normal;
    margin-top: 2px;
}

/* ── DUA KOLOM ── */
.tc {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    table-layout: fixed;
}
.tc td {
    width: 50%;
    vertical-align: top;
    text-align: justify;
    padding: 0 12px 0 0;
    font-size: 10.5pt;
}
.tc td.right {
    padding: 0 0 0 12px;
}

/* ── JUDUL PASAL ── */
.ptitle {
    text-align: center;
    font-weight: bold;
    font-size: 10.5pt;
    text-transform: uppercase;
    padding: 10px 0 3px;
}

/* ── PARAGRAF ── */
p { text-align: justify; margin-bottom: 5px; font-size: 10.5pt; }

/* ── LIST ── */
.num { margin: 5px 0 8px 0; padding-left: 0; list-style: none; }
.num li { margin-bottom: 5px; padding-left: 22px; text-indent: -22px; text-align: justify; }
.num li .n { display: inline-block; width: 22px; }

.alpha { margin: 4px 0 4px 22px; padding-left: 0; list-style: none; }
.alpha li { margin-bottom: 4px; padding-left: 22px; text-indent: -22px; text-align: justify; }
.alpha li .n { display: inline-block; width: 22px; }

/* ── SPASI ── */
.s4  { height: 4px;  display: block; }
.s8  { height: 8px;  display: block; }
.s12 { height: 12px; display: block; }
.s20 { height: 20px; display: block; }
.s50 { height: 50px; display: block; }

/* ── NOMOR HALAMAN ── */
.pgn { text-align: center; margin-top: 16px; font-size: 10pt; }

/* ── SIGNATURE LINE ── */
.sline { border-top: 1px solid #000; width: 200px; margin-top: 55px; margin-bottom: 3px; }
</style>
</head>
<body>
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $tgl = Carbon::parse($kontrak->tanggal_kontrak);

    // Nama hari & tanggal ID
    $hariId = $tgl->isoFormat('dddd');
    $tglId  = $tgl->isoFormat('D MMMM YYYY');

    // Nama hari & tanggal EN
    $hariEn = $tgl->locale('en')->isoFormat('dddd');
    $tglEn  = $tgl->locale('en')->isoFormat('D MMMM YYYY');

    Carbon::setLocale('id');

    $kendaraanList = ($kontrak->penawaran && $kontrak->penawaran->items)
        ? $kontrak->penawaran->items->filter(fn($i) => $i->kendaraan)
        : collect();

    $dV = $kontrak->durasi_value  ?? '';
    $dS = $kontrak->durasi_satuan ?? '';

    $mulaiId   = $kontrak->tanggal_kontrak
        ? Carbon::parse($kontrak->tanggal_kontrak)->isoFormat('D MMMM YYYY') : '..........';
    $selesaiId = $kontrak->tanggal_selesai
        ? Carbon::parse($kontrak->tanggal_selesai)->isoFormat('D MMMM YYYY') : '..........';
    $mulaiEn   = $kontrak->tanggal_kontrak
        ? Carbon::parse($kontrak->tanggal_kontrak)->locale('en')->isoFormat('D MMMM YYYY') : '..........';
    $selesaiEn = $kontrak->tanggal_selesai
        ? Carbon::parse($kontrak->tanggal_selesai)->locale('en')->isoFormat('D MMMM YYYY') : '..........';

    $namaPerush  = $setting->nama_perusahaan    ?? 'PT. Rental Kendaraan Indonesia';
    $alamatPerush= $setting->alamat             ?? 'Jl. Sudirman Jakarta Pusat';
    $telpPerush  = $setting->telepon            ?? '';
    $faxPerush   = '';
    $emailPerush = $setting->email              ?? '';
    $namaBank    = $setting->nama_bank          ?? 'BCA';
    $noRek       = $setting->nomor_rekening     ?? '272-1420-878';
    $atasNama    = $setting->atas_nama_rekening ?? $kontrak->pihak_pertama;
    $nilaiKontrak= $kontrak->penawaran ? $kontrak->penawaran->total : 0;

    $ttdId = $tgl->isoFormat('D MMMM YYYY');
    $ttdEn = $tgl->locale('en')->isoFormat('D MMMM YYYY');
    Carbon::setLocale('id');
@endphp

{{-- ════════════════════════════════════════
     HALAMAN 1
════════════════════════════════════════ --}}
<div class="page">

<div class="doc-title">
    <div class="t1">PERJANJIAN SEWA MENYEWA KENDARAAN</div>
    <div class="t2">CAR RENTAL AGREEMENT</div>
    <div class="t3">NO : {{ $kontrak->no_kontrak }}</div>
</div>

<table class="tc">
<tr>
    <td>
        <p>Pada hari ini, {{ $hariId }}, tanggal {{ $tglId }}.
        kami yang bertanda tangan dibawah ini, masing-masing:</p>
    </td>
    <td class="right">
        <p>On this day, {{ $hariEn }}, {{ $tglEn }},
        we the undersigned, respectively:</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td>
        <p><strong>1. {{ $namaPerush }},</strong> suatu perseroan terbatas yang memiliki
        kantor terdaftar di {{ $alamatPerush }}, dalam hal ini diwakili oleh
        <strong>{{ $kontrak->pihak_pertama }}</strong>,
        selanjutnya disebut "Pihak Pertama"; dan</p>
    </td>
    <td class="right">
        <p><strong>1. {{ $namaPerush }},</strong> a limited liability company, having its
        registered office at {{ $alamatPerush }}, in this matter represented by
        <strong>{{ $kontrak->pihak_pertama }}</strong>,
        hereinafter referred to as the "First Party"; and</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td>
        <p>2. <strong>{{ $kontrak->pihak_kedua }}.,</strong> yang beralamat sesuai di
        KTP ………………………., dalam hal ini diwakili oleh
        <strong>{{ $kontrak->contact_kedua ?: '………………………….' }}</strong>,
        selanjutnya disebut sebagai "Pihak Kedua"</p>
    </td>
    <td class="right">
        <p><strong>2. {{ $kontrak->pihak_kedua }}.,</strong> which is based on the KTP
        ………………………., in this matter represented by
        <strong>{{ $kontrak->contact_kedua ?: '………………………….' }}</strong>,
        hereinafter referred to as the "Second Party".</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td>
        <p>Para Pihak sepakat untuk mengadakan Perjanjian Sewa Menyewa Kendaraan
        ('Perjanjian') dengan kondisi sebagai berikut:</p>
    </td>
    <td class="right">
        <p>The Parties hereby agree to enter into the Car Rental Agreement ('Agreement')
        under the following terms and condition:</p>
    </td>
</tr>

<tr>
    <td><div class="ptitle">PASAL 1<br>DATA-DATA KENDARAAN</div></td>
    <td class="right"><div class="ptitle">ARTICLE 1<br>VEHICLE DATA</div></td>
</tr>

<tr>
    <td>
        <p>PIHAK PERTAMA telah menyerahkan kendaran untuk disewa oleh PIHAK KEDUA
        dan PIHAK KEDUA telah menerima kendaraan tersebut yang tertuang dalam berita
        acara serah terima kendaraan dan check list yang ditandatangani oleh Para Pihak
        dan merupakan bagian yang tak terpisahkan dari Perjanjian ini. Adapun spesifikasi
        dan jumlah kendaraan tertuang dalam lampiran 1 (satu) Perjanjian ini.</p>
        @foreach($kendaraanList as $item)
        @php $k = $item->kendaraan; @endphp
        <p style="margin-left:10px;">&#x2013; {{ $k->merk ?? '-' }}
            @if($k->warna) {{ $k->warna }}@endif
            @if($k->tahun_pembuatan) ({{ $k->tahun_pembuatan }})@endif,
            No.Pol: <strong>{{ $k->nopol ?? '-' }}</strong>@if($k->no_rangka), No.Rangka: {{ $k->no_rangka }}@endif,
            Qty: {{ $item->qty ?? 1 }} unit</p>
        @endforeach
    </td>
    <td class="right">
        <p>The FIRST PARTY shall provide a car to be rented by the SECOND PARTY and
        the SECOND PARTY shall receive the said car that is specifically stated in the
        vehicle handover form and check list form signed by The Parties that constitute
        and inseparable part of this Agreement. The car specification and quantity are
        described in detail in attachment 1 (one) of the Agreement.</p>
        @foreach($kendaraanList as $item)
        @php $k = $item->kendaraan; @endphp
        <p style="margin-left:10px;">&#x2013; {{ $k->merk ?? '-' }}
            @if($k->warna) {{ $k->warna }}@endif
            @if($k->tahun_pembuatan) ({{ $k->tahun_pembuatan }})@endif,
            Plate: <strong>{{ $k->nopol ?? '-' }}</strong>,
            Qty: {{ $item->qty ?? 1 }} unit</p>
        @endforeach
    </td>
</tr>
</table>

<div class="pgn">1</div>
</div>


{{-- ════════════════════════════════════════
     HALAMAN 2
════════════════════════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td><div class="ptitle">PASAL 2<br>MASA SEWA</div></td>
    <td class="right"><div class="ptitle">ARTICLE 2<br>RENTAL PERIOD</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1.</span>Mobil tersebut diatas disewa oleh PIHAK KEDUA untuk jangka waktu
                <strong>{{ $dV }} {{ $dS }}</strong> &#x2013; terhitung sejak tanggal serah terima kendaraan
                (<strong>{{ $mulaiId }}</strong> s/d <strong>{{ $selesaiId }}</strong>).</li>
            <li><span class="n">2.</span>Apabila kendaraan tidak dikembalikan tepat waktu, maka akan dikenakan
                biaya sewa harian sebesar Rp. 400.000,- / hari.</li>
            <li><span class="n">3.</span>Pengiriman kendaraan paling lambat 3 minggu setelah diterimanya SPK
                (Surat Perintah Kerja) atau PO (Purchase Order).</li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>The Car mentioned above shall be rented by the Second Party for a period
                of <strong>{{ $dV }} {{ $dS }}</strong> &#x2013; commencing after the delivery of cars
                (<strong>{{ $mulaiEn }}</strong> to <strong>{{ $selesaiEn }}</strong>).</li>
            <li><span class="n">2.</span>If the vehicle is not returned on time, it will be charged a daily rental
                of Rp.400.000,- / day</li>
            <li><span class="n">3.</span>Delivery of cars at least 3 (three) weeks after the receipt of Work
                Authorization or PO (Purchase Order)</li>
        </ul>
    </td>
</tr>

<tr>
    <td><div class="ptitle">PASAL 3<br>HARGA SEWA DAN PEMBAYARAN</div></td>
    <td class="right"><div class="ptitle">ARTICLE 3<br>RENTAL PRICE AND PAYMENT</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1.</span>Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.@if($nilaiKontrak) Total: <strong>Rp {{ number_format($nilaiKontrak,0,',','.') }},-</strong>@endif</li>
            <li><span class="n">2.</span>Sewa mobil yang dibayarkan sudah termasuk :<br>
                &nbsp;&nbsp;-Pemeliharaan dan reparasi Kendaraan<br>
                &nbsp;&nbsp;- Biaya STNK/KIR<br>
                &nbsp;&nbsp;- Asuransi All Risk<br>
                &nbsp;&nbsp;- Kendaraan Pengganti<br><br>
                Dan tidak termasuk<br>
                &nbsp;&nbsp;- PPN 11%<br>
                &nbsp;&nbsp;- PPH 23<br>
                &nbsp;&nbsp;- Bensin, parkir dan tol.</li>
            <li><span class="n">3.</span>PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada
                PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi
                pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang
                mendukung.@if($kontrak->perjanjian_pembayaran) Jatuh tempo: <strong>{{ Carbon::parse($kontrak->perjanjian_pembayaran)->isoFormat('D MMMM YYYY') }}</strong>.@endif</li>
            <li><span class="n">4.</span>Pembayaran dilakukan melalui:<br>
                &nbsp;&nbsp;Nama Bank &nbsp;&nbsp;: <strong>{{ $namaBank }}</strong><br>
                &nbsp;&nbsp;No. Rekening : <strong>{{ $noRek }}</strong><br>
                &nbsp;&nbsp;Atas nama &nbsp;&nbsp;: <strong>{{ $atasNama }}</strong></li>
            <li><span class="n">5.</span>Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana
                disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai
                sewa per hari untuk setiap hari keterlambatan.</li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>The car rental fee is written on the attachment 1 (one) of the Agreement.@if($nilaiKontrak) Total: <strong>Rp {{ number_format($nilaiKontrak,0,',','.') }},-</strong>@endif</li>
            <li><span class="n">2.</span>The car rental fee paid include:<br>
                &nbsp;&nbsp;- Maintenance And Repair<br>
                &nbsp;&nbsp;- Motor Vehicle Document (STNK/KIR)<br>
                &nbsp;&nbsp;- All Risk Insurance<br>
                &nbsp;&nbsp;-Replacement car<br><br>
                And exclude<br>
                &nbsp;&nbsp;- Value Added Tax 11%<br>
                &nbsp;&nbsp;- Income tax<br>
                &nbsp;&nbsp;- Gasoline, parking and toll fee</li>
            <li><span class="n">3.</span>The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental
                fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the
                current month, along with attach invoice, VAT certificate and other supporting
                document.@if($kontrak->perjanjian_pembayaran) Due date: <strong>{{ Carbon::parse($kontrak->perjanjian_pembayaran)->locale('en')->isoFormat('D MMMM YYYY') }}</strong>.@endif</li>
            <li><span class="n">4.</span>Payment is done through:<br>
                &nbsp;&nbsp;BankName &nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ $namaBank }}</strong><br>
                &nbsp;&nbsp;Account No. &nbsp;: <strong>{{ $noRek }}</strong><br>
                &nbsp;&nbsp;Account Name: <strong>{{ $atasNama }}</strong></li>
            <li><span class="n">5.</span>If The SECOND PARTY fails to perform the payment obligation mentioned
                above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total
                payable rent per day.</li>
        </ul>
    </td>
</tr>

</table>
<div class="pgn">2</div>
</div>


{{-- ════════════════════════════════════════
     HALAMAN 3
════════════════════════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td><div class="ptitle">PASAL 4<br>KEWAJIBAN PIHAK PERTAMA</div></td>
    <td class="right"><div class="ptitle">ARTICLE 4<br>OBLIGATION OF THE FIRST PARTY</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1.</span>PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan
                di pool/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK
                PERTAMA sehingga mobil dalam keadaan siap beroperasi/baik selama masa sewa.</li>
            <li><span class="n">2.</span>Batas jarak tempuh kendaraan adalah sebesar 2500 km/bulan</li>
            <li><span class="n">3.</span>Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.</li>
            <li><span class="n">4.</span>Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada
                di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.</li>
            <li><span class="n">5.</span>PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil
                yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh
                empat jam) jam.</li>
            <li><span class="n">6.</span>PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu
                mencapai pemakaian 60.000 km atau setelah 2 tahun.</li>
            <li><span class="n">7.</span>PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk
                tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:
                <ul class="alpha">
                    <li><span class="n">a.</span>Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan
                        polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan
                        dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab
                        PIHAK KEDUA.</li>
                    <li><span class="n">b.</span>Dalam hal kecelakaan/kehilangan/pencurian mobil yang disewa, dimana
                        kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih
                        menjadi tanggung jawab PIHAK KEDUA.</li>
                    <li><span class="n">c.</span>Selama proses pengurusan</li>
                </ul>
            </li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>The FIRST PARTY is obligated to maintain and repair the rented car in
                the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST
                PARTY so that the car is in good condition during the rental period.</li>
            <li><span class="n">2.</span>The maximum distance travel in a month is 2500 km</li>
            <li><span class="n">3.</span>The mileage can be accumulated and the surcharge will be billed in the end
                of the rent period.</li>
            <li><span class="n">4.</span>If the car breakdown when it is out of town due to engine failure, additional
                costs will be charged for on call services.</li>
            <li><span class="n">5.</span>FIRST PARTY has agreed to provide replacement car in case of the car rent
                by SECOND PARTY is being repaired for more than 24 (twenty four) hours.</li>
            <li><span class="n">6.</span>THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use
                first or after 2 years.</li>
            <li><span class="n">7.</span>The FIRST PARTY is obligated to insure the rented car with all risk insurance
                but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following
                provisions:
                <ul class="alpha">
                    <li><span class="n">a.</span>Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or
                        maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per
                        occurrence. Exceeding amount becomes the SECOND PARTY responsibility.</li>
                    <li><span class="n">b.</span>In the event of damage/loss/theft of the car, hence the claim is rejected
                        by the insurance company and in effect will hold responsible fully to the
                        cost effect of occurrence.</li>
                    <li><span class="n">c.</span>While undergoing the process of</li>
                </ul>
            </li>
        </ul>
    </td>
</tr>

</table>
<div class="pgn">3</div>
</div>


{{-- ════════════════════════════════════════
     HALAMAN 4
════════════════════════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td>
        <ul class="alpha" style="margin-left:22px;">
            <li><span class="n">c.</span>pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak
                mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar
                10% dari uang pertanggungan yang tertera di polis.</li>
            <li><span class="n">d.</span>Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair,
                PIHAK KEDUA berkewajiban membayar biaya resiko sendiri</li>
        </ul>
    </td>
    <td class="right">
        <ul class="alpha" style="margin-left:22px;">
            <li><span class="n">c.</span>insurance claim for the loss/theft of the car, The SECOND PARTY will not
                receive replacement car and responsible to pay own risk claim of 10% of the
                insured sum that is written in the insurance policy.</li>
            <li><span class="n">d.</span>In the event of accident that requires body repair, the SECOND PARTY is
                obligated to pay own risk</li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s12"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 5<br>KEWAJIBAN PIHAK KEDUA</div></td>
    <td class="right"><div class="ptitle">PASAL 5<br>OBLIGATION OF THE SECOND PARTY</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1</span>PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta
                menyediakan tempat parkir yang aman.</li>
            <li><span class="n">2.</span>Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.</li>
            <li><span class="n">3.</span>Bila terjadi kehilangan/pencurian mobil, PIHAK KEDUA berkewajiban untuk
                memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan
                kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat
                Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang
                dikeluarkan menjadi tanggung jawab PIHAK KEDUA.</li>
            <li><span class="n">4.</span>PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil
                tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan</li>
            <li><span class="n">5.</span>PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil,
                menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.</li>
            <li><span class="n">6.</span>PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK
                PERTAMA dalam hal:
                <ul class="alpha">
                    <li><span class="n">a.</span>Perubahan nama/alamat PIHAK</li>
                </ul>
            </li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>SECOND PARTY assures to keep and protect the rented car and also provide
                a safe parking lot.</li>
            <li><span class="n">2.</span>During rental period, the car will be parked in SECOND PARTY's parking lot.</li>
            <li><span class="n">3.</span>In the event of loss/theft, SECOND PARTY has obligation to inform FIRST PARTY
                within 24 hour. Together, both parties report to the Police station in order to
                obtain the Lost Report Information Letter and Vehicle Motor Document (STNK)
                Blocking Letter that is issued by Police Department (POLDA). All costs incurred
                will be responsibility of SECOND PARTY.</li>
            <li><span class="n">4.</span>The SECOND PARTY is not allowed to re-let and/or transfer its right in any
                nature to any other party including making the car as a guarantee.</li>
            <li><span class="n">5.</span>The SECOND PARTY is not allowed to change and/or replace the form of the
                car, to add, replace, or detach any part of the car without previously notify
                the FIRST PARTY.</li>
            <li><span class="n">6.</span>The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :
                <ul class="alpha">
                    <li><span class="n">a.</span>If the SECOND PARTY intends to</li>
                </ul>
            </li>
        </ul>
    </td>
</tr>

</table>
<div class="pgn">4</div>
</div>


{{-- ════════════════════════════════════════
     HALAMAN 5
════════════════════════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td>
        <ul class="alpha" style="margin-left:22px;">
            <li><span class="n">b.</span>Jika ada perubahan dalam fungsi atau kegunaan mobil.</li>
        </ul>
        <ul class="num" style="margin-top:4px;">
            <li><span class="n">7.</span>PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk
                balap/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan
                alasan komersial atau alasan lainnya selain alasan domestik atau sosial.</li>
            <li><span class="n">8.</span>Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula,
                dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya
                waktu.</li>
        </ul>
    </td>
    <td class="right">
        <ul class="alpha" style="margin-left:22px;">
            <li><span class="n">b.</span>In case of any change of car utilization purpose.</li>
        </ul>
        <ul class="num" style="margin-top:4px;">
            <li><span class="n">7.</span>The SECOND PARTY is not allowed to use the car in/for any car race,
                political campaign, criminal action, carrying any passenger for commercial
                purpose and/or any other purpose besides the domestic and social purposes.</li>
            <li><span class="n">8.</span>To return the car on the expiration of the lease duration in original
                condition, save for reasonable wear and tear due the passage of time.</li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 6<br>STNK</div></td>
    <td class="right"><div class="ptitle">ARTICLE 6<br>MOTOR VEHICLE DOCUMENT (STNK)</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1.</span>Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan
                dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.</li>
            <li><span class="n">2.</span>PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul
                sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan
                perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.</li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>The extension cost of the Motor Vehicle Document (STNK) shall be paid by
                the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration
                date.</li>
            <li><span class="n">2.</span>The SECOND PARTY is responsible for all the costs born for the lost of
                vehicle legal document (STNK) and also for any delay in extension process
                because of the SECOND PARTY negligence.</li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td><div class="ptitle">PASAL 7<br>PENGEMUDI</div></td>
    <td class="right"><div class="ptitle">ARTICLE 7<br>DRIVER</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1.</span>Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.</li>
            <li><span class="n">2.</span>PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang
                ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK
                KEDUA dan tidak di parkir di tempat yang telah ditentukan.</li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>The Car lease shall be driven by SECOND PARTY' s driver</li>
            <li><span class="n">2.</span>SECOND PARTY shall bear all losses and legal consequences caused when the
                vehicle is being driven by a driver who was assigned by the SECOND PARTY and
                when the vehicle is not parked in the designated parking lot.</li>
        </ul>
    </td>
</tr>

</table>
<div class="pgn">5</div>
</div>


{{-- ════════════════════════════════════════
     HALAMAN 6
════════════════════════════════════════ --}}
<div class="page">
<table class="tc">

<tr>
    <td><div class="ptitle">PASAL 8<br>PEMUTUSAN DAN PERPANJANGAN<br>PERJANJIAN</div></td>
    <td class="right"><div class="ptitle">ARTICLE 8<br>TERMINATION AND EXTENSION OF<br>AGREEMENT</div></td>
</tr>
<tr>
    <td>
        <ul class="num">
            <li><span class="n">1.</span>Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal
                pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian
                ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim
                surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA
                dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat
                peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK
                PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA
                menyerahkan kendaraan kepada PIHAK KEDUA.</li>
            <li><span class="n">2.</span>Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah
                kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu
                kesatuan yang tidak terpisahkan dengan Perjanjian ini.</li>
            <li><span class="n">3.</span>Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum
                masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh
                persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak
                dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama
                digunakan</li>
            <li><span class="n">4.</span>Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada
                lampiran perjanjian ini.</li>
        </ul>
    </td>
    <td class="right">
        <ul class="num">
            <li><span class="n">1.</span>If the SECOND PARTY fails to fulfill one of the stipulations with respect to
                the payment obligation to the FIRST PARTY as regulated in this agreement, the
                FIRST PARTY shall have the right to terminate this agreement by delivering a
                written reprimand 3 (three) calendar days before the termination to the SECOND
                PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives
                the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the
                car to the FIRST PARTY in a good condition and at the location where the FIRST
                PARTY hand over the car to the SECOND PARTY at the first time.</li>
            <li><span class="n">2.</span>Both parties can extend the contract period and or add the quantity of cars
                rented with a supplemental agreement (addendum), which is an inseparable part
                of the Agreement.</li>
            <li><span class="n">3.</span>Both PARTIES can terminate/cancel this Agreement before the expiry date but
                liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent
                and the rental fee that has been paid upfront could not be refunded, unless the
                car is often damaged during use</li>
            <li><span class="n">4.</span>All other matter which is not covered in the articles will be covered in
                the attachment.</li>
        </ul>
    </td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

<tr>
    <td><div class="ptitle">ARTICLE 9<br>PEMBERITAHUAN</div></td>
    <td class="right"><div class="ptitle">ARTICLE 9<br>NOTICE</div></td>
</tr>
<tr>
    <td>
        <p>Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan
        perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau
        dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:</p>
    </td>
    <td class="right">
        <p>Any notice, request and other communications relating to this agreement must be
        made in writing and submitted in person or delivered through courier or facsimile
        to parties in the following addresses:</p>
    </td>
</tr>

</table>
<div class="pgn">6</div>
</div>


{{-- ════════════════════════════════════════
     HALAMAN 7
════════════════════════════════════════ --}}
<div class="page">
<table class="tc">

{{-- Alamat Pihak (lanjutan Pasal 9) --}}
<tr>
    <td>
        <p><strong>PIHAK PERTAMA</strong></p>
        <p>{{ strtoupper($namaPerush) }}</p>
        <p>{{ $alamatPerush }}</p>
        @if($telpPerush)<p>Telp. {{ $telpPerush }}</p>@endif
        @if($faxPerush)<p>Fax. {{ $faxPerush }}</p>@endif
        <span class="s12"></span>
        <p><strong>PIHAK KEDUA</strong></p>
        <p>………………………………</p>
        <p>………………………………</p>
        <p>………………………………</p>
        <p>………………………………</p>
        <p>Hp. .............................</p>
    </td>
    <td class="right">
        <p><strong>THE FIRST PARTY</strong></p>
        <p>{{ strtoupper($namaPerush) }}</p>
        <p>{{ $alamatPerush }}</p>
        @if($telpPerush)<p>Telp. {{ $telpPerush }}</p>@endif
        @if($faxPerush)<p>Fax. {{ $faxPerush }}</p>@endif
        <span class="s12"></span>
        <p><strong>THE SECOND PARTY</strong></p>
        <p>………………………………</p>
        <p>………………………………</p>
        <p>………………………………</p>
        <p>………………………………</p>
        <p>Hp. .............................</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s20"></span></td></tr>

{{-- PASAL 10 --}}
<tr>
    <td><div class="ptitle">ARTICLE 10<br>PENUTUP</div></td>
    <td class="right"><div class="ptitle">ARTICLE 10<br>CLOSING PROVISION</div></td>
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
    <td class="right">
        <p>Any dispute arising form this Agreement of Vehicle Rent shall be settled in
        deliberation by Both Parties, and if the dispute cannot be settled in deliberation,
        Both Parties shall agree to elect the general and permanent domicile at the office
        clerk of the District Court of Jakarta Selatan, in Jakarta.</p>
        <span class="s8"></span>
        <p>In witness whereof this Agreement of Vehicle Rent was made and signed by Both
        Parties in duplicate, each duty stamped and having the same legal force.</p>
    </td>
</tr>
<tr><td colspan="2"><span class="s20"></span></td></tr>

{{-- Tanggal --}}
<tr>
    <td><p>Jakarta, {{ $ttdId }}</p></td>
    <td class="right"><p>Jakarta, {{ $ttdEn }}</p></td>
</tr>
<tr><td colspan="2"><span class="s8"></span></td></tr>

{{-- Label TTD --}}
<tr>
    <td><p>PIHAK PERTAMA/THE FIRST PARTY</p></td>
    <td class="right"><p>PIHAK KEDUA/THE SECOND PARTY</p></td>
</tr>

{{-- Spasi TTD --}}
<tr><td colspan="2"><span class="s50"></span></td></tr>

{{-- Garis & nama TTD --}}
<tr>
    <td>
        <div class="sline"></div>
        <p>{{ $kontrak->pihak_pertama }}</p>
    </td>
    <td class="right">
        <div class="sline"></div>
        <p>………………………………</p>
    </td>
</tr>

</table>
<div class="pgn">7</div>
</div>

</body>
</html>
