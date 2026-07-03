<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder Penawaran</title>
</head>

<body style="font-family:Arial,sans-serif;line-height:1.6;">

    @if ($tipe == 'reminder')
        <h2 style="color:#f59e0b;">
            ⏰ Reminder Penawaran
        </h2>

        <p>
            Penawaran berikut akan segera berakhir.
        </p>
    @else
        <h2 style="color:#dc2626;">
            ⚠ Penawaran Telah Berakhir
        </h2>

        <p>
            Penawaran berikut telah melewati masa berlakunya.
        </p>
    @endif


    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;margin-bottom:20px;">

        <tr>
            <td width="35%"><b>No Penawaran</b></td>
            <td>{{ $penawaran->no_penawaran }}</td>
        </tr>

        <tr>
            <td><b>Customer</b></td>
            <td>{{ $penawaran->customer_name }}</td>
        </tr>

        @if (!empty($penawaran->nama_perusahaan))
            <tr>
                <td><b>Perusahaan</b></td>
                <td>{{ $penawaran->nama_perusahaan }}</td>
            </tr>
        @endif

        @if (!empty($penawaran->nama_person))
            <tr>
                <td><b>Contact Person</b></td>
                <td>{{ $penawaran->nama_person }}</td>
            </tr>
        @endif

        @if (!empty($penawaran->contact_person))
            <tr>
                <td><b>Telepon</b></td>
                <td>{{ $penawaran->contact_person }}</td>
            </tr>
        @endif

        <tr>
            <td><b>Tanggal Penawaran</b></td>
            <td>{{ \Carbon\Carbon::parse($penawaran->tanggal_penawaran)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td><b>Berlaku Sampai</b></td>
            <td>{{ \Carbon\Carbon::parse($penawaran->tanggal_berakhir)->format('d-m-Y') }}</td>
        </tr>

        @if ($tipe == 'reminder')
            <tr>
                <td><b>Sisa Waktu</b></td>
                <td>{{ $hari }} hari lagi</td>
            </tr>
        @else
            <tr>
                <td><b>Terlambat</b></td>
                <td>{{ $hari }} hari</td>
            </tr>
        @endif

        @if (!empty($penawaran->grand_total))
            <tr>
                <td><b>Grand Total</b></td>
                <td>
                    Rp {{ number_format($penawaran->grand_total, 0, ',', '.') }}
                </td>
            </tr>
        @endif

    </table>


    @if ($penawaran->items && $penawaran->items->count())

        <h3>Daftar Kendaraan</h3>

        <table width="100%" border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">

            <thead style="background:#f3f4f6;">
                <tr>
                    <th>No</th>
                    <th>No Polisi</th>
                    <th>Merk</th>
                    <th>Tahun Unit</th>
                    <th>Qty</th>
                    <th>Durasi</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

                @php
                    $grandTotal = 0;
                @endphp

                @foreach ($penawaran->items as $no => $item)
                    @php
                        $subtotal = $item->qty * $item->price * $item->durasi;
                        $grandTotal += $subtotal;
                    @endphp

                    <tr>

                        <td align="center">
                            {{ $no + 1 }}
                        </td>

                        <td>
                            {{ $item->kendaraan->nopol ?? '-' }}
                        </td>

                        <td>
                            {{ $item->kendaraan->merk ?? '-' }}
                        </td>

                        <td align="center">
                            {{ $item->tahun_unit }}
                        </td>

                        <td align="center">
                            {{ $item->qty }}
                        </td>

                        <td align="center">
                            {{ $item->durasi }}
                            {{ $item->satuan_durasi }}
                        </td>

                        <td align="right">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>

                        <td align="right">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </td>

                    </tr>
                @endforeach

                <tr style="font-weight:bold;background:#f8f8f8;">
                    <td colspan="7" align="right">
                        Grand Total
                    </td>

                    <td align="right">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>

            </tbody>

        </table>

    @endif


    @if ($tipe == 'reminder')
        <p style="margin-top:20px;">
            Mohon segera melakukan tindak lanjut terhadap penawaran sebelum masa berlaku berakhir.
        </p>
    @else
        <p style="margin-top:20px;">
            Masa berlaku penawaran telah habis. Silakan buat penawaran baru apabila customer masih berminat.
        </p>
    @endif


    <br>

    <p>
        Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
    </p>

</body>

</html>
