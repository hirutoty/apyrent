<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder KIR Kendaraan</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6;">

@if ($tipe == 'reminder')

    <h2 style="color:#f59e0b;">
        ⏰ Reminder KIR Kendaraan
    </h2>

    <p>
        KIR kendaraan berikut akan segera jatuh tempo.
    </p>

    <table cellpadding="6" cellspacing="0" border="1">
        <tr>
            <td><b>No Polisi</b></td>
            <td>{{ $kir->kendaraan->nopol ?? '-' }}</td>
        </tr>

        <tr>
            <td><b>Merk</b></td>
            <td>{{ $kir->kendaraan->merk ?? '-' }}</td>
        </tr>

        <tr>
            <td><b>No Uji</b></td>
            <td>{{ $kir->no_uji }}</td>
        </tr>

        <tr>
            <td><b>Masa Berlaku</b></td>
            <td>{{ \Carbon\Carbon::parse($kir->masa_berlaku)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td><b>Sisa Waktu</b></td>
            <td>{{ $hari }} hari lagi</td>
        </tr>

        <tr>
            <td><b>Biaya</b></td>
            <td>Rp {{ number_format($kir->biaya, 0, ',', '.') }}</td>
        </tr>
    </table>

    <p>
        Mohon segera melakukan perpanjangan sebelum masa berlaku habis.
    </p>

    <p style="margin-top:20px;">
        <a href="https://apy.creativegamastudio.com/admin/kir"
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
            Bayar KIR
        </a>
    </p>

@else

    <h2 style="color:red;">
        ⚠ KIR Kendaraan Terlambat
    </h2>

    <p>
        KIR kendaraan berikut telah melewati tanggal masa berlaku.
    </p>

    <table cellpadding="6" cellspacing="0" border="1">
        <tr>
            <td><b>No Polisi</b></td>
            <td>{{ $kir->kendaraan->nopol ?? '-' }}</td>
        </tr>

        <tr>
            <td><b>Merk</b></td>
            <td>{{ $kir->kendaraan->merk ?? '-' }}</td>
        </tr>

        <tr>
            <td><b>No Uji</b></td>
            <td>{{ $kir->no_uji }}</td>
        </tr>

        <tr>
            <td><b>Masa Berlaku</b></td>
            <td>{{ \Carbon\Carbon::parse($kir->masa_berlaku)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td><b>Terlambat</b></td>
            <td>{{ $hari }} hari</td>
        </tr>

        <tr>
            <td><b>Biaya</b></td>
            <td>Rp {{ number_format($kir->biaya, 0, ',', '.') }}</td>
        </tr>
    </table>

    <p>
        Segera lakukan perpanjangan agar tidak terkena sanksi.
    </p>

    <p style="margin-top:20px;">
        <a href="https://apy.creativegamastudio.com/admin/kir"
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
            Bayar KIR
        </a>
    </p>

@endif

<br>

<p>
    Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
</p>

</body>

</html>