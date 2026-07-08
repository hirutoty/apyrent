<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Invoice - {{ $setting?->nama_perusahaan ?? 'APY Rent A Car' }}</title>
  <style>
    body {
      font-family: "Times New Roman", serif;
      margin: 0;
      padding: 0;
      color: #000;
      background: #fff;
      font-size: 16px;
      line-height: 1.2;
    }

    .paper {
      width: 100%;
      max-width: 210mm; /* A4 */
      margin: 0 auto;
      padding: 0;
      box-sizing: border-box;
    }

    /* ===== Header ===== */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .brand img {
      height: 40px;
    }
    .invoice-box {
      flex: 1;
      text-align: center;
    }
    .invoice-box h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 1.8px;
      border-bottom: 2px solid #000;
      display: inline-block;
      padding: 5px 0;
    }
    .inv-no {
      font-size: 15px;
      margin-top: 6px;
    }

    /* ===== Customer Info ===== */
    .info {
      margin-top: 12px;
    }
    .info .line {
      margin: 4px 0;
    }
    .label {
      display: inline-block;
      width: 160px;
      font-weight: bold;
    }

    /* ===== Table ===== */
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      margin: 20px 0;
    }
    th, td {
      border: 1px solid #000;
      padding: 6px 8px;
      vertical-align: top;
    }
    th {
      text-align: center;
      font-weight: bold;
      background: #f2f2f2;
    }
    td.text-center { text-align: center; }
    td.text-right  { text-align: right; }
    td.text-left   { text-align: left; }

    tfoot td {
      font-weight: bold;
    }

    .table tbody td {
      border-bottom: none !important;
    }
    .no-border-bottom {
      border-bottom: none !important;
    }

    /* ===== After Table ===== */
    .after-table {
      page-break-inside: avoid;
      page-break-before: auto;
      margin-top: 10px;
    }

    /* ===== Bank Info & Notes ===== */
    .bank,
    .payment-note {
      margin-top: 16px;
      font-size: 14px;
      line-height: 1.5;
    }
    .bank p,
    .payment-note p {
      margin: 0;
      padding: 0;
    }

    /* ===== Signature ===== */
    .sign {
      display: table;
      width: 100%;
      margin-top: 25px;
      table-layout: fixed;
    }
    .sign .box {
      display: table-cell;
      width: 50%;
      text-align: center;
      vertical-align: top;
      padding: 0 10px;
    }
    .sign .title {
      margin-top: 8px;
      font-size: 15px;
    }
    .sign .spacer {
      height: 75px;
    }
    .sign .name {
      font-weight: bold;
      font-size: 15px;
      margin-top: 5px;
    }

    /* ===== Footer ===== */
    .footer {
      margin-top: 25px;
      text-align: center;
      font-size: 15px;
      font-weight: bold;
    }
    .footnote {
      margin-top: 8px;
      text-align: center;
      font-size: 14px;
      line-height: 1.5;
    }
    .footnote div {
      margin: 3px 0;
    }

    @media print {
      body { margin: 0; padding: 0; }
      .paper { margin: 0; box-shadow: none; }
      .main-table { page-break-inside: auto; }
      .main-table tr { page-break-inside: avoid; }
      .after-table { page-break-inside: avoid; page-break-before: auto; }
    }
  </style>
</head>
<body>
  <div class="paper">

    <!-- Header -->
    <div class="header">
      <div class="brand">
        @php
          $logoPath = $setting?->logo
            ? public_path($setting->logo)
            : public_path('images/icon.png');
          if (!file_exists($logoPath)) {
            $logoPath = public_path('images/icon.png');
          }
          $logoMime = mime_content_type($logoPath) ?: 'image/png';
          $logoSrc  = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
        @endphp
        <img src="{{ $logoSrc }}" alt="Logo">
      </div>
      <div class="invoice-box">
        <h1>INVOICE</h1>
        <div class="inv-no">No. {{ $invoice->invoice_no }}</div>
      </div>
    </div>

    <!-- Customer Info -->
    @php
      $vehicle = $invoice->kendaraan
        ? $invoice->kendaraan->merk . ' ' . $invoice->kendaraan->nopol
        : '';
    @endphp
    <div class="info">
      <div class="line"><span class="label">No. Order</span>: {{ $invoice->order_no }}</div>
      <div class="line"><span class="label">Nama Customer</span>: <b>{{ $invoice->customer_name }}</b></div>
      <div class="line"><span class="label">Alamat</span>: {{ $invoice->customer_address }}</div>
      <div class="line"><span class="label">Contact Person</span>: {{ $invoice->contact_person }}</div>
      <div class="line"><span class="label">Telephone</span>: {{ $invoice->telephone }}</div>
      <div class="line"><span class="label">Kendaraan</span>: {{ $vehicle }}</div>
    </div>

    <!-- Table Items -->
    <table class="table table-bordered">
      <thead>
        <tr>
          <th style="width:18%;">Periode</th>
          <th style="width:30%;">Remaks</th>
          <th style="width:7%;">QTY</th>
          <th style="width:20%;">Gaji, OT &amp; OPR<br>{{ $invoice->satuan }}</th>
          <th style="width:25%;">Sub Total</th>
        </tr>
      </thead>
      <tbody>
        @php $subTotal = 0; @endphp

        @foreach($invoice->periodes as $periode)
          @php $rowspan = $periode->remaks->count(); @endphp

          @foreach($periode->remaks as $i => $item)
            <tr>
              @if($i === 0)
                <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                  {{ \Carbon\Carbon::parse($periode->periode_awal)->translatedFormat('d F Y') }}
                  -
                  {{ \Carbon\Carbon::parse($periode->periode_akhir)->translatedFormat('d F Y') }}
                </td>
              @endif

              <td>{!! nl2br(e($item->remaks)) !!}</td>
              <td class="text-center no-border-bottom">{{ $item->qty ?? '-' }}</td>
              <td class="text-right no-border-bottom">
                {{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}
              </td>
              <td class="text-right no-border-bottom">
                Rp {{ number_format($item->subtotal ?? ($item->qty * $item->price), 0, ',', '.') }}
              </td>
            </tr>
            @php $subTotal += ($item->subtotal ?? ($item->qty * $item->price)); @endphp
          @endforeach
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td></td>
          <td colspan="2" class="text-left">Total</td>
          <td class="text-right">Rp {{ number_format($subTotal, 0, ',', '.') }}</td>
          <td class="text-right">Rp {{ number_format($subTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td></td>
          <td colspan="3" class="text-left">
            PPN{{ floatval($invoice->ppn ?? 0) > 0 ? '' : '' }}
          </td>
          <td class="text-right">Rp {{ number_format(floatval($invoice->ppn ?? 0), 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td></td>
          <td colspan="3" class="text-left">Sub Total</td>
          <td class="text-right">Rp {{ number_format($subTotal + floatval($invoice->ppn ?? 0), 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td></td>
          <td colspan="3" class="text-left">Potongan PPh</td>
          <td class="text-right">Rp {{ number_format(floatval($invoice->pph ?? 0), 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td colspan="4" class="text-center">Total Invoice</td>
          <td class="text-right">
            Rp {{ number_format($grand_total, 0, ',', '.') }}
          </td>
        </tr>
        <tr>
          <td colspan="5" class="text-center">
            # {{ $terbilang }} #
          </td>
        </tr>
      </tfoot>
    </table>

    <!-- After Table -->
    <div class="after-table">

      <!-- Bank Info -->
      <div class="bank">
        <p>
          Please make payment by wire transfer to:<br>
          <b>{{ $setting?->atas_nama_rekening }}</b><br>
          {{ $setting?->nama_bank }}<br>
          IDR Acc. No.: {{ $setting?->nomor_rekening }}
        </p>
      </div>

      <!-- Payment Note -->
      <div class="payment-note">
        <p>
          Bukti Transfer mohon di fax ke nomor (021) 837-92927 / email ke apy@cbn.net.id<br>
          Apabila bukti pembayaran belum kami terima maka kami belum dapat memproses pembayaran Invoice tersebut.
        </p>
      </div>

      <!-- Signature -->
      <div class="sign">
        <div class="box">
          <div>Jakarta, {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') }}</div>
          <div>{{ $invoice->pengirim }}</div>
          <div class="title">Prepared By,</div>
          <div class="spacer" style="position:relative;">
            @if(!empty($logoSrc))
              <img src="{{ $logoSrc }}" style="height:40px; opacity:0.35; position:absolute; left:50%; transform:translateX(-50%); top:8px;">
            @endif
            @if(!empty($ttdStaffSrc))
              <img src="{{ $ttdStaffSrc }}" style="height:80px; position:absolute; left:50%; transform:translateX(-50%); top:4px;">
            @endif
          </div>
          <div class="name">{{ $invoice->name_staff }}</div>
          <div class="title">{{ $invoice->staff }}</div>
        </div>
        <div class="box">
          <div>&nbsp;</div>
          <div>&nbsp;</div>
          <div class="title">Approved By,</div>
          <div class="spacer">
            @if(!empty($ttdDirekturSrc))
              <img src="{{ $ttdDirekturSrc }}" style="height:80px; margin-top:4px;">
            @endif
          </div>
          <div class="name">{{ $invoice->name_direktur }}</div>
          <div class="title">{{ $invoice->direktur }}</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="footer">{{ $setting?->nama_perusahaan ?? 'PT. ANUGERAH PANCA YOGA' }}</div>
      <div class="footnote">
        <div>{{ $setting?->alamat }}</div>
        <div>Telp: {{ $setting?->telepon }}{{ $setting?->email ? ' | ' . $setting->email : '' }}{{ $setting?->website ? ' | ' . $setting->website : '' }}</div>
      </div>
    </div>

  </div>
</body>
</html>
