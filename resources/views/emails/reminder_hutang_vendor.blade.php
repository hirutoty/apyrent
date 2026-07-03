<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder Hutang Vendor</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6;">

    @if ($tipe == 'reminder')
        <h2 style="color:#f59e0b;">
            ⏰ Reminder Hutang Vendor
        </h2>

        <p>
            Ada hutang vendor yang akan segera jatuh tempo.
        </p>

        <table cellpadding="6" cellspacing="0" border="1">
            <tr>
                <td><b>Vendor</b></td>
                <td>{{ $hutang->nama_vendor }}</td>
            </tr>

            <tr>
                <td><b>Kategori</b></td>
                <td>{{ $hutang->kategori }}</td>
            </tr>

            <tr>
                <td><b>Nominal</b></td>
                <td>Rp {{ number_format($hutang->nominal, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td><b>Dibayar</b></td>
                <td>Rp {{ number_format($hutang->dibayar, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td><b>Sisa</b></td>
                <td>Rp {{ number_format($hutang->sisa, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td><b>Jatuh Tempo</b></td>
                <td>{{ \Carbon\Carbon::parse($hutang->jatuh_tempo)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td><b>Sisa Waktu</b></td>
                <td>{{ $hari }} hari lagi</td>
            </tr>
        </table>

        <p>
            Mohon segera melakukan pembayaran sebelum jatuh tempo.
        </p>

        <p style="margin-top:20px;">
            <a href="https://apy.creativegamastudio.com/admin/hutang-vendor"
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
                Lihat Hutang <span class="typcn typcn-vendor-android"></span>
            </a>
        </p>

    @else
        <h2 style="color:red;">
            ⚠ Hutang Vendor Terlambat
        </h2>

        <p>
            Hutang vendor berikut telah melewati tanggal jatuh tempo.
        </p>

        <table cellpadding="6" cellspacing="0" border="1">
            <tr>
                <td><b>Vendor</b></td>
                <td>{{ $hutang->nama_vendor }}</td>
            </tr>

            <tr>
                <td><b>Kategori</b></td>
                <td>{{ $hutang->kategori }}</td>
            </tr>

            <tr>
                <td><b>Nominal</b></td>
                <td>Rp {{ number_format($hutang->nominal, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td><b>Dibayar</b></td>
                <td>Rp {{ number_format($hutang->dibayar, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td><b>Sisa</b></td>
                <td>Rp {{ number_format($hutang->sisa, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td><b>Jatuh Tempo</b></td>
                <td>{{ \Carbon\Carbon::parse($hutang->jatuh_tempo)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td><b>Keterlambatan</b></td>
                <td>{{ abs($hari) }} hari terlambat</td>
            </tr>
        </table>

        <p>
            Segera lakukan pembayaran untuk menghindari masalah keuangan.
        </p>

        <p style="margin-top:20px;">
            <a href="https://apy.creativegamastudio.com/admin/hutang-vendor"
                style="
                background-color:#dc2626;
                color:#ffffff;
                text-decoration:none;
                padding:12px 24px;
                border-radius:6px;
                display:inline-block;
                font-weight:bold;
                font-family:Arial,sans-serif;
            ">
                Bayar Sekarang
            </a>
        </p>
    @endif

    <br>

    <p>
        Email ini dikirim otomatis oleh Sistem Manajemen Rental.
    </p>

</body>

</html>