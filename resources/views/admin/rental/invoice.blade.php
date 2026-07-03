<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Rental</title>

    <style>
        .payment-note {
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #dbeafe;
            background: #fff7ed;
            font-size: 10px;
            line-height: 1.5;
        }

        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .border {
            border: 1px solid #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .logo {
            height: 55px;
        }

        td,
        th {
            padding: 4px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #1e40af;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .detail-table th {
            background: #1e40af;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border: 1px solid #dbeafe;
        }

        .detail-table td {
            border: 1px solid #dbeafe;
            padding: 8px;
        }

        .detail-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .summary-row {
            background: #eff6ff;
        }

        .total-row {
            background: #1e40af;
            color: #fff;
            font-weight: bold;
        }

        .terbilang-box {
            margin-top: 12px;
            padding: 10px;
            border-left: 4px solid #1e40af;
            background: #eff6ff;
            font-size: 10px;
        }

        .bank-box {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #dbeafe;
            background: #f8fafc;
        }
    </style>
</head>

<body>

    @php
        $setting = $setting ?? null;

        $dp = $rental->nominal_dp ?? 0;
        $biayaDriver = ($rental->biaya_driver ?? 0) * ($rental->durasi_hari ?? 1);
        $grandTotal = $rental->total_biaya ?? 0;
        $sisaPelunasan = $grandTotal - $dp;

        // Harga satuan & label durasi kendaraan
        $satuan = '';
        $hargaSatuan = 0;

        if ($rental->durasi_bulan) {
            $satuan = 'Bulan';
            $hargaSatuan = ($rental->kendaraan->harga_sewa_per_hari ?? 0) * 30;
        } elseif ($rental->durasi_hari) {
            $satuan = 'Hari';
            $hargaSatuan = $rental->kendaraan->harga_sewa_per_hari ?? 0;
        } elseif ($rental->durasi_jam) {
            $satuan = 'Jam';
            $hargaSatuan = $rental->kendaraan->harga_sewa_per_jam ?? 0;
        } elseif ($rental->durasi_tahun) {
            $satuan = 'Tahun';
            $hargaSatuan = ($rental->kendaraan->harga_sewa_per_hari ?? 0) * 30 * 12;
        }

        // Sub total kendaraan saja (tanpa driver)
        $subTotalKendaraan = $grandTotal - $biayaDriver;

        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $invoiceNo =
            $rental->id .
            '/Inv/APY/' .
            $bulanRomawi[\Carbon\Carbon::parse($rental->created_at)->month] .
            '/' .
            \Carbon\Carbon::parse($rental->created_at)->year;
    @endphp

    {{-- ── HEADER ──────────────────────────────────────────── --}}
    <table>
        <tr>
            <td width="40%">
                @if ($setting?->logo)
                    <img src="{{ public_path($setting->logo) }}" class="logo">
                @endif
            </td>
            <td width="60%">
                <div style="font-size:30px; font-weight:bold; color:#1e40af; letter-spacing:3px;">
                    INVOICE
                </div>
                <div class="bold">No. {{ $invoiceNo }}</div>
            </td>
        </tr>
    </table>

    <br>

    {{-- ── CUSTOMER ─────────────────────────────────────────── --}}
    <table>
        <tr>
            <td width="18%">Nama Customer</td>
            <td width="2%">:</td>
            <td>{{ $rental->member->nama_member ?? '-' }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $rental->member->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Telepon</td>
            <td>:</td>
            <td>{{ $rental->member->kontak_member ?? '-' }}</td>
        </tr>
    </table>

    <br>

    {{-- ── DETAIL TABLE ─────────────────────────────────────── --}}
    <table class="detail-table">
        <thead>
            <tr>
                <th width="18%">Periode</th>
                <th width="25%">Keterangan</th>
                <th width="10%">Qty</th>
                <th width="12%">Durasi</th>
                @if (!empty($rental->biaya_driver) && $rental->biaya_driver > 0)
                    <th width="12%">Tujuan</th>
                @endif
                <th width="15%">Harga Satuan</th>
                <th width="20%">Jumlah</th>
            </tr>
        </thead>
        <tbody>

            {{-- ── Baris 1: Sewa Kendaraan ── --}}
            <tr>
                <td class="center">
                    {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d M Y') }}
                    <br>s/d<br>
                    {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d M Y') }}
                </td>
                <td>
                    <strong>Sewa Kendaraan</strong><br>
                    {{ $rental->kendaraan->merk ?? '-' }}<br>
                    {{ $rental->kendaraan->nopol ?? '-' }}
                </td>
                <td class="center">1</td>
                <td class="center">
                    @if ($rental->durasi_bulan)
                        {{ $rental->durasi_bulan }} Bulan
                    @elseif ($rental->durasi_hari)
                        {{ $rental->durasi_hari }} Hari
                    @elseif ($rental->durasi_tahun)
                        {{ $rental->durasi_tahun }} Tahun
                    @else
                        -
                    @endif
                </td>

                @if (!empty($rental->biaya_driver) && $rental->biaya_driver > 0)
                    <td class="center">

                        {{ $rental->tujuan }}
                    </td>
                @endif
                <td class="right">
                    Rp {{ number_format($hargaSatuan, 0, ',', '.') }}
                    <br><small>/{{ $satuan }}</small>
                </td>
                <td class="right">
                    Rp {{ number_format($subTotalKendaraan, 0, ',', '.') }}
                </td>
            </tr>



            @if (!empty($rental->biaya_driver) && $rental->biaya_driver > 0)

            {{-- Sub Total --}}
            
            <tr class="summary-row">
                <td colspan="6" class="right"><strong>Biaya Driver</strong></td>
                <td class="right">
                    <strong>Rp {{ number_format($biayaDriver, 0, ',', '.') }}</strong>
                </td>
            </tr>
            
                {{-- DP --}}
                @if ($dp > 0)
                    <tr>
                        <td colspan="6" class="right"><strong>DP (Down Payment)</strong></td>
                        <td class="right">Rp {{ number_format($dp, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="right"><strong>Sisa Pelunasan</strong></td>
                        <td class="right">Rp {{ number_format($sisaPelunasan, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td colspan="6" class="right">TOTAL INVOICE</td>
                    <td class="right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>


            @else
                {{-- Sub Total --}}
                <tr class="summary-row">
                    <td colspan="5" class="right"><strong>Sub Total</strong></td>
                    <td class="right">
                        <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                    </td>
                </tr>



                {{-- DP --}}
                @if ($dp > 0)
                    <tr>
                        <td colspan="5" class="right"><strong>DP (Down Payment)</strong></td>
                        <td class="right">Rp {{ number_format($dp, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="right"><strong>Sisa Pelunasan</strong></td>
                        <td class="right">Rp {{ number_format($sisaPelunasan, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td colspan="5" class="right">TOTAL INVOICE</td>
                    <td class="right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>

            @endif

        </tbody>
    </table>

    {{-- ── TERBILANG ────────────────────────────────────────── --}}
    @php
        function penyebut($nilai)
        {
            $nilai = abs($nilai);
            $huruf = [
                '',
                'Satu',
                'Dua',
                'Tiga',
                'Empat',
                'Lima',
                'Enam',
                'Tujuh',
                'Delapan',
                'Sembilan',
                'Sepuluh',
                'Sebelas',
            ];
            if ($nilai < 12) {
                return ' ' . $huruf[$nilai];
            } elseif ($nilai < 20) {
                return penyebut($nilai - 10) . ' Belas';
            } elseif ($nilai < 100) {
                return penyebut($nilai / 10) . ' Puluh' . penyebut($nilai % 10);
            } elseif ($nilai < 200) {
                return ' Seratus' . penyebut($nilai - 100);
            } elseif ($nilai < 1000) {
                return penyebut($nilai / 100) . ' Ratus' . penyebut($nilai % 100);
            } elseif ($nilai < 2000) {
                return ' Seribu' . penyebut($nilai - 1000);
            } elseif ($nilai < 1000000) {
                return penyebut($nilai / 1000) . ' Ribu' . penyebut($nilai % 1000);
            } elseif ($nilai < 1000000000) {
                return penyebut($nilai / 1000000) . ' Juta' . penyebut($nilai % 1000000);
            } elseif ($nilai < 1000000000000) {
                return penyebut($nilai / 1000000000) . ' Miliar' . penyebut($nilai % 1000000000);
            }
            return '';
        }
        function terbilang($nilai)
        {
            return trim(penyebut($nilai)) . ' Rupiah';
        }
    @endphp

    <div class="mt-10 small">
        <strong>Terbilang :</strong><br>
        <em>{{ terbilang($grandTotal) }}</em>
    </div>

    {{-- ── INFO PEMBAYARAN ─────────────────────────────────── --}}
    <div class="bank-box">
        <strong>INFORMASI PEMBAYARAN</strong><br><br>
        Nama Bank &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ $setting->nama_bank ?? '-' }}</strong><br>
        Nomor Rekening : <strong>{{ $setting->nomor_rekening ?? '-' }}</strong><br>
        Atas Nama &nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ $setting->atas_nama_rekening ?? '-' }}</strong>
    </div>

    <div class="payment-note">
        <strong>Catatan Pembayaran :</strong><br>
        Bukti transfer mohon di fax ke nomor {{ $setting->telepon ?? '-' }} atau email ke
        <strong>{{ $setting->email ?? '-' }}</strong><br>
        Apabila bukti pembayaran belum kami terima maka kami belum dapat memproses pembayaran invoice tersebut.
    </div>

    {{-- ── FOOTER ──────────────────────────────────────────── --}}
    <div class="footer">
        <strong>{{ $setting->nama_perusahaan ?? '-' }}</strong><br>
        Telp : {{ $setting->telepon ?? '-' }}
        | Email : {{ $setting->email ?? '-' }}
        @if (!empty($setting?->website))
            | {{ $setting->website }}
        @endif
        <br>
        {{ $setting->alamat ?? '-' }}
    </div>

</body>

</html>
