<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Invoice - INV-RENTAL-{{ str_pad($rental->id, 4, '0', STR_PAD_LEFT) }}</title>
  <style>
    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 12pt;
      color: #000;
      margin: 0;
      padding: 0;
    }
    .paper {
      padding: 18mm 16mm 12mm 16mm;
    }
    /* INFO TABLE */
    .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .info-table td { padding: 1px 0; font-size: 11pt; vertical-align: top; }
    .info-table td.lbl { width: 140px; font-weight: bold; }
    .info-table td.sep { width: 10px; }

    /* MAIN INVOICE TABLE */
    .inv-table { width: 100%; border-collapse: collapse; margin-top: 14px; margin-bottom: 30px; font-size: 10.5pt; }
    .inv-table th {
      border: 1px solid #000;
      padding: 5px 6px;
      text-align: center;
      background-color: #f0f0f0;
      font-weight: bold;
    }
    .inv-table td {
      border: 1px solid #000;
      padding: 5px 6px;
      vertical-align: middle;
    }
    .inv-table td.c { text-align: center; }
    .inv-table td.r { text-align: right; }
    .inv-table td.l { text-align: left; }
    .inv-table tfoot td { font-weight: bold; }

    /* BANK INFO */
    .bank-info { margin margin-top: 20px; font-size: 10.5pt; line-height: 1.6; }

    /* PAYMENT NOTE */
    .pay-note { margin-top: 8px; font-size: 9.5pt; line-height: 1.5; }

    /* SIGNATURE TABLE */
    .sign-table { width: 100%; border-collapse: collapse; margin-top: 22px; }
    .sign-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 10px; border: none; }
    .sign-table td.left { text-align: left; }
    .sign-spacer { height: 70px; text-align: center; }
    .sign-spacer.left { text-align: left; }
    .sign-spacer img { max-height: 66px; max-width: 150px; }
    .sign-line { border-top: 1px solid #000; padding-top: 3px; font-weight: bold; font-size: 11pt; margin-top: 4px; }
    .sign-pos { font-size: 10.5pt; margin-top: 2px; }

    /* FOOTER */
    .footer-co { margin-top: 100px; text-align: center; font-weight: bold; font-size: 11pt; }
    .footnote { margin-top: 4px; text-align: center; font-size: 9pt; line-height: 1.6; }

    @page { margin: 0mm; }
  </style>
</head>
<body>
<div class="paper">

  {{-- ============ HEADER ============ --}}
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
    <tr>
      <td width="130" style="vertical-align:middle;">
        @if(!empty($logoSrc))
          <img src="{{ $logoSrc }}" style="height:80px;max-width:160px;" alt="Logo">
        @endif
      </td>
      <td style="text-align:center; vertical-align:middle;">
        <div style="font-size:18pt; font-weight:bold; letter-spacing:3px; border-bottom:2.5px solid #000; display:inline-block; padding-bottom:3px;">INVOICE</div>
        <div style="font-size:11pt; margin-top:5px;">No. INV-RENTAL-{{ str_pad($rental->id, 4, '0', STR_PAD_LEFT) }}</div>
      </td>
      <td width="130"></td>
    </tr>
  </table>

  {{-- ============ CUSTOMER INFO ============ --}}
  @php
    $kendaraanList = $rental->kendaraan
        ? ($rental->kendaraan->merk . ' ' . $rental->kendaraan->nopol)
        : '-';
  @endphp

  <table class="info-table">
    <tr><td class="lbl">No. Order</td><td class="sep">:</td><td>INV-RENTAL-{{ str_pad($rental->id, 4, '0', STR_PAD_LEFT) }}</td></tr>
    <tr><td class="lbl">Nama Customer</td><td class="sep">:</td><td><strong>{{ $rental->member->nama_pelanggan ?? '-' }}</strong></td></tr>
    <tr><td class="lbl">Alamat</td><td class="sep">:</td><td>{{ $rental->member->alamat ?? '-' }}</td></tr>
    <tr><td class="lbl">Contact Person</td><td class="sep">:</td><td>{{ $rental->member->kontak_pelanggan ?? '-' }}</td></tr>
    <tr><td class="lbl">Telephone</td><td class="sep">:</td><td>{{ $rental->member->kontak_pelanggan ?? '-' }}</td></tr>
    <tr><td class="lbl">Kendaraan</td><td class="sep">:</td><td>{{ $kendaraanList }}</td></tr>
  </table>

  {{-- ============ INVOICE TABLE ============ --}}
  @php
    $subTotal = 0;

    $durasi = null;
    $satuanLabel = '';
    if ($rental->durasi_tahun)       { $durasi = $rental->durasi_tahun;  $satuanLabel = 'Tahun'; }
    elseif ($rental->durasi_bulan)   { $durasi = $rental->durasi_bulan;  $satuanLabel = 'Bulan'; }
    elseif ($rental->durasi_hari)    { $durasi = $rental->durasi_hari;   $satuanLabel = 'Hari'; }

    $periodeMulai = $rental->tanggal_mulai
        ? \Carbon\Carbon::parse($rental->tanggal_mulai)->translatedFormat('d F Y')
        : '-';
    $periodeAkhir = $rental->tanggal_selesai
        ? \Carbon\Carbon::parse($rental->tanggal_selesai)->translatedFormat('d F Y')
        : '-';

    $satuanHeader = $durasi ? ('Car Rent / ' . $satuanLabel) : 'Car Rent/Day';

    // Baris item
    $items = [];

    // 1. Sewa kendaraan
    $hargaPerHari = (float) ($rental->kendaraan->harga_sewa_per_hari ?? 0);
    $biayaDasar   = (float) ($rental->biaya_dasar ?? 0);
    $subTotal    += $biayaDasar;
    $items[] = [
        'remaks'   => 'Sewa Kendaraan: ' . $kendaraanList
                      . ($durasi ? "\nDurasi: {$durasi} {$satuanLabel}" : ''),
        'qty'      => 1,
        'price'    => $hargaPerHari,
        'subtotal' => $biayaDasar,
    ];

    // 2. Driver (jika ada)
    if ($rental->nama_driver || ($rental->biaya_driver ?? 0) > 0) {
        $biayaDriver = (float) ($rental->biaya_driver ?? 0) * ($durasi ?? 1);
        $subTotal   += $biayaDriver;
        $items[] = [
            'remaks'   => 'Biaya Driver' . ($rental->nama_driver ? ': ' . $rental->nama_driver : ''),
            'qty'      => $durasi ?? 1,
            'price'    => (float) ($rental->biaya_driver ?? 0),
            'subtotal' => $biayaDriver,
        ];
    }

    // 3. Biaya tambahan (jika ada)
    $biayaTambahan = (float) ($rental->biaya_tambahan_total ?? 0);
    if ($biayaTambahan > 0) {
        $subTotal += $biayaTambahan;
        $items[] = [
            'remaks'   => 'Biaya Tambahan',
            'qty'      => 1,
            'price'    => $biayaTambahan,
            'subtotal' => $biayaTambahan,
        ];
    }

    $ppnNom   = 0;
    $pphNom   = 0;
    $afterPpn = $subTotal + $ppnNom;
    $grand    = (float) ($rental->total_biaya ?? ($afterPpn - $pphNom));
  @endphp

  <table class="inv-table">
    <thead>
      <tr>
        <th style="width:18%;">Periode</th>
        <th style="width:37%;">Remaks</th>
        <th style="width:7%;">QTY</th>
        <th style="width:19%;">{{ $satuanHeader }}</th>
        <th style="width:19%;">Sub Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $i => $item)
        <tr>
          @if($i === 0)
            <td class="c" rowspan="{{ count($items) }}" style="font-size:9.5pt;">
              {{ $periodeMulai }}
              @if($periodeAkhir !== $periodeMulai)
                –<br>{{ $periodeAkhir }}
              @endif
            </td>
          @endif
          <td class="l">{!! nl2br(e($item['remaks'])) !!}</td>
          <td class="c">{{ $item['qty'] }}</td>
          <td class="r">Rp &nbsp;{{ number_format($item['price'], 0, ',', '.') }}</td>
          <td class="r">Rp &nbsp;{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>

    {{-- ============ INVOICE TABLE FOOTER ============ --}}
    <tfoot>
      <tr>
        <td rowspan="4"></td>
        <td colspan="2" class="l">Total</td>
        <td class="r">{{ number_format($subTotal, 0, ',', '.') }}</td>
        <td class="r">{{ number_format($subTotal, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td colspan="2" class="l">PPN 10%</td>
        <td></td>
        <td class="r">{{ $ppnNom > 0 ? number_format($ppnNom, 0, ',', '.') : '-' }}</td>
      </tr>
      <tr>
        <td colspan="3" class="l">Sub Total</td>
        <td class="r">{{ number_format($afterPpn, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td colspan="3" class="l">Pot PPh 2%</td>
        <td class="r">{{ $pphNom > 0 ? number_format($pphNom, 0, ',', '.') : '-' }}</td>
      </tr>
      <tr>
        <td colspan="4" class="c">Total Invoice</td>
        <td class="r">{{ number_format($grand, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td colspan="5" class="c" style="font-style:italic;">
          # {{ $terbilang }} #
        </td>
      </tr>
    </tfoot>
  </table>

  {{-- ============ BANK & NOTE ============ --}}
  <div class="bank-info">
    Please make payment by wire transfer to:<br>
    <strong>{{ $setting?->atas_nama_rekening }}</strong><br>
    {{ $setting?->nama_bank }}, {{ $setting?->nama_cabang_bank ?? '' }}<br>
    IDR Acc. No.: {{ $setting?->nomor_rekening }}
  </div>

  <div class="pay-note">
    Bukti Transfer mohon di fax ke nomor (021) 837-92927 / email ke apy@cbn.net.id<br>
    Apabila bukti pembayaran belum kami terima maka kami belum dapat memproses
    <br>
    pembayaran Invoice tersebut
  </div>

  {{-- ============ SIGNATURE ============ --}}
  <table class="sign-table">
    <tr>
      <td class="left">
        <div style="font-size:11pt;">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div style="font-size:11pt; margin-bottom:6px;">{{ $setting?->nama_perusahaan ?? '' }}</div>
        <div style="font-style:italic; font-size:11pt;">Prepared By,</div>
        <div class="sign-spacer left">
          <img style="position: absolute; opacity: 40%;" src="{{ $logoSrc }}" alt="Logo">
        </div>
        <div class="" style="text-align:left;">Administrasi</div>
        <div class="sign-pos" style="text-align:left;">{{ $setting?->nama_perusahaan ?? '' }}</div>
      </td>
      <td style="text-align:right;">
        <div style="font-size:11pt;">&nbsp;</div>
        <div style="font-size:11pt; margin-bottom:6px;">&nbsp;</div>
        <div style="font-style:italic; font-size:11pt;">Approved By,</div>
        <div class="sign-spacer" style="text-align:right;"></div>
        <div class="" style="text-align:right;">{{ $rental->member->nama_pelanggan ?? '' }}</div>
        <div class="sign-pos" style="text-align:right;">Customer</div>
      </td>
    </tr>
  </table>

  {{-- ============ FOOTER ============ --}}
  <div class="footer-co">{{ $setting?->nama_perusahaan ?? 'PT. ANUGERAH PANCA YOGA' }}</div>
  <div class="footnote">
    <div>Head Office : Kompl. Transkop No.16, Jl. Prof. Supomo SH. Pancoran, Jakarta 12870 Ph. (+62-21) 835 4565</div>
    <div>Branch : Jl. Dr. Saharjo No.131, Jakarta 12860 Ph (+62-21) 8379 2927, 8378 17151 Fax (+62-21) 8379 2927</div>
    <div>{{ $setting?->website ?? 'www.apy-rentacar.com' }}</div>
  </div>

</div>
</body>
</html>
