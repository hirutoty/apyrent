<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder Service Kendaraan</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6;">

@if ($tipe === 'reminder')

    <h2 style="color:#f59e0b;">
        ⏰ Reminder Service Kendaraan
    </h2>

    <p>
        Jadwal service kendaraan berikut akan segera jatuh tempo.
    </p>

    <table cellpadding="6" cellspacing="0" border="1">
        <tr>
            <td><b>No Polisi</b></td>
            <td>{{ $reminder->kendaraan->nopol ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Merk Kendaraan</b></td>
            <td>{{ $reminder->kendaraan->merk ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Nama Reminder</b></td>
            <td>{{ $reminder->nama_reminder }}</td>
        </tr>
        <tr>
            <td><b>Tanggal Mulai</b></td>
            <td>{{ \Carbon\Carbon::parse($reminder->tanggal_mulai)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><b>Interval</b></td>
            <td>{{ $reminder->interval_nilai }} {{ $reminder->interval_satuan }}</td>
        </tr>
        <tr>
            <td><b>Jatuh Tempo</b></td>
            <td>{{ \Carbon\Carbon::parse($reminder->tanggal_jatuh_tempo)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><b>Sisa Waktu</b></td>
            <td>{{ $sisaHari }} hari lagi</td>
        </tr>
        @if ($reminder->keterangan)
        <tr>
            <td><b>Keterangan</b></td>
            <td>{{ $reminder->keterangan }}</td>
        </tr>
        @endif
    </table>

    <p>
        Mohon segera melakukan pengecekan dan service sebelum tanggal jatuh tempo.
    </p>

    <p style="margin-top:20px;">
        <a href="{{ url('/admin/reminder-service') }}"
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
            Lihat Reminder Service
        </a>
    </p>

@else

    <h2 style="color:red;">
        ⚠ Reminder Service Kendaraan Jatuh Tempo
    </h2>

    <p>
        Jadwal service kendaraan berikut telah melewati tanggal jatuh tempo dan telah otomatis ditambahkan ke daftar <b>Mobil Bermasalah</b>.
    </p>

    <table cellpadding="6" cellspacing="0" border="1">
        <tr>
            <td><b>No Polisi</b></td>
            <td>{{ $reminder->kendaraan->nopol ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Merk Kendaraan</b></td>
            <td>{{ $reminder->kendaraan->merk ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Nama Reminder</b></td>
            <td>{{ $reminder->nama_reminder }}</td>
        </tr>
        <tr>
            <td><b>Tanggal Mulai</b></td>
            <td>{{ \Carbon\Carbon::parse($reminder->tanggal_mulai)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><b>Interval</b></td>
            <td>{{ $reminder->interval_nilai }} {{ $reminder->interval_satuan }}</td>
        </tr>
        <tr>
            <td><b>Jatuh Tempo</b></td>
            <td>{{ \Carbon\Carbon::parse($reminder->tanggal_jatuh_tempo)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><b>Terlambat</b></td>
            <td>{{ abs($sisaHari) }} hari</td>
        </tr>
        @if ($reminder->keterangan)
        <tr>
            <td><b>Keterangan</b></td>
            <td>{{ $reminder->keterangan }}</td>
        </tr>
        @endif
    </table>

    <p>
        Segera lakukan pengecekan dan service kendaraan tersebut.
    </p>

    <p style="margin-top:20px;">
        <a href="{{ url('/admin/service-detail') }}"
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
            Lihat Mobil Bermasalah
        </a>
    </p>

@endif

<br>
<p>
    Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
</p>

</body>
</html>
