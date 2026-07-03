<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder Asuransi Kendaraan</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6;">

    @if ($tipe == 'reminder')
        <h2 style="color:#f59e0b;">
            ⏰ Reminder Asuransi Kendaraan
        </h2>

        <p>
            Asuransi kendaraan berikut akan segera jatuh tempo.
        </p>

        <table cellpadding="6" cellspacing="0" border="1">
            <tr>
                <td><b>No Polisi</b></td>
                <td>{{ $asuransi->kendaraan->nopol }}</td>
            </tr>

            <tr>
                <td><b>Merk</b></td>
                <td>{{ $asuransi->kendaraan->merk }}</td>
            </tr>

            <tr>
                <td><b>Jenis Asuransi</b></td>
                {{ $asuransi->jenisAsuransi->nama_jenis }}

            </tr>

            <tr>
                <td><b>Jatuh Tempo</b></td>
                <td>{{ \Carbon\Carbon::parse($asuransi->tgl_berakhir)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td><b>Sisa Waktu</b></td>
                <td>{{ $hari }} hari lagi</td>
            </tr>

            <tr>
                <td><b>Nominal</b></td>
                <td>Rp {{ number_format($asuransi->biaya, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p>
            Mohon segera melakukan pembayaran sebelum tanggal jatuh tempo.
        </p>
        <p style="margin-top:20px;">
            <a href="https://apy.creativegamastudio.com/admin/asuransi-kendaraan"
                style="
            background-color:#2563eb;
            color:#ffffff;
            text-decoration:none;
            padding:12px 24px;
            border-radius:6px;
            display:inline-block;
            font-weight:bold;
            font-family:Arial,sans-serif;
       ">
                Bayar Asuransi
            </a>
        </p>
    @else
        <h2 style="color:red;">
            ⚠ Asuransi Kendaraan Terlambat
        </h2>

        <p>
            Asuransi kendaraan berikut telah melewati tanggal jatuh tempo.
        </p>

        <table cellpadding="6" cellspacing="0" border="1">
            <tr>
                <td><b>No Polisi</b></td>
                <td>{{ $asuransi->kendaraan->nopol }}</td>
            </tr>

            <tr>
                <td><b>Merk</b></td>
                <td>{{ $asuransi->kendaraan->merk }}</td>
            </tr>

            <tr>
                <td><b>Jenis Asuransi</b></td>
                {{ $asuransi->jenisAsuransi->nama_jenis }}

            </tr>

            <tr>
                <td><b>Jatuh Tempo</b></td>
                <td>{{ \Carbon\Carbon::parse($asuransi->tgl_berakhir)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td><b>Sisa Waktu</b></td>
                <td>{{ $hari }} hari lagi</td>
            </tr>

            <tr>
                <td><b>Nominal</b></td>
                <td>Rp {{ number_format($asuransi->biaya, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p>
            Segera lakukan pembayaran agar tidak terkena denda.
        </p>
        <p style="margin-top:20px;">
            <a href="https://apy.creativegamastudio.com/admin/asuransi-kendaraan"
                style="
            background-color:#2563eb;
            color:#ffffff;
            text-decoration:none;
            padding:12px 24px;
            border-radius:6px;
            display:inline-block;
            font-weight:bold;
            font-family:Arial,sans-serif;
       ">
                Bayar Asuransi
            </a>
        </p>
    @endif

    <br>

    <p>
        Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
    </p>

</body>

</html>
