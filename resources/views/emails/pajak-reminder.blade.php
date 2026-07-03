<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder Pajak Kendaraan</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6;">

    @if ($tipe == 'reminder')
        <h2 style="color:#f59e0b;">
            ⏰ Reminder Pajak Kendaraan
        </h2>

        <p>
            Pajak kendaraan berikut akan segera jatuh tempo.
        </p>

        <table cellpadding="6" cellspacing="0" border="1">
            <tr>
                <td><b>No Polisi</b></td>
                <td>{{ $pajak->kendaraan->nopol }}</td>
            </tr>

            <tr>
                <td><b>Merk</b></td>
                <td>{{ $pajak->kendaraan->merk }}</td>
            </tr>

            <tr>
                <td><b>Jenis Pajak</b></td>
                <td>{{ $pajak->jenis_pajak }}</td>
            </tr>

            <tr>
                <td><b>Jatuh Tempo</b></td>
                <td>{{ \Carbon\Carbon::parse($pajak->jatuh_tempo)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td><b>Sisa Waktu</b></td>
                <td>{{ $hari }} hari lagi</td>
            </tr>

            <tr>
                <td><b>Nominal</b></td>
                <td>Rp {{ number_format($pajak->nominal, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p>
            Mohon segera melakukan pembayaran sebelum tanggal jatuh tempo.
        </p>
        <p style="margin-top:20px;">
            <a href="https://apy.creativegamastudio.com/admin/pajak"
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
                Bayar Pajak
            </a>
        </p>
    @else
        <h2 style="color:red;">
            ⚠ Pajak Kendaraan Terlambat
        </h2>

        <p>
            Pajak kendaraan berikut telah melewati tanggal jatuh tempo.
        </p>

        <table cellpadding="6" cellspacing="0" border="1">
            <tr>
                <td><b>No Polisi</b></td>
                <td>{{ $pajak->kendaraan->nopol }}</td>
            </tr>

            <tr>
                <td><b>Merk</b></td>
                <td>{{ $pajak->kendaraan->merk }}</td>
            </tr>

            <tr>
                <td><b>Jenis Pajak</b></td>
                <td>{{ $pajak->jenis_pajak }}</td>
            </tr>

            <tr>
                <td><b>Jatuh Tempo</b></td>
                <td>{{ \Carbon\Carbon::parse($pajak->jatuh_tempo)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td><b>Terlambat</b></td>
                <td>{{ $hari }} hari</td>
            </tr>

            <tr>
                <td><b>Nominal</b></td>
                <td>Rp {{ number_format($pajak->nominal, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p>
            Segera lakukan pembayaran agar tidak terkena denda.
        </p>
        <p style="margin-top:20px;">
            <a href="https://apy.creativegamastudio.com/admin/pajak"
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
                Bayar Pajak
            </a>
        </p>
    @endif

    <br>

    <p>
        Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
    </p>

</body>

</html>
