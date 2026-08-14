<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Service Kendaraan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6; color:#374151;">

{{-- ============================================================
     TIPE: part_limit — ringkasan semua part baru yang limit hari ini
     ============================================================ --}}
@if ($tipe === 'part_limit')

    <h2 style="color:#dc2626;">🔔 Part Kendaraan Melewati Batas Interval</h2>

    <p>
        Sistem mendeteksi <strong>{{ count($partLimitRows) }} part</strong> kendaraan yang telah melewati
        batas interval pada <strong>{{ now()->format('d M Y') }}</strong>.
        Reminder otomatis telah dibuat untuk masing-masing part berikut:
    </p>

    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse; width:100%; font-size:13px;">
        <thead style="background-color:#fee2e2;">
            <tr>
                <th style="text-align:left;">#</th>
                <th style="text-align:left;">Kendaraan</th>
                <th style="text-align:left;">Part</th>
                <th style="text-align:left;">Kategori</th>
                <th style="text-align:left;">Posisi</th>
                <th style="text-align:left;">Interval</th>
                <th style="text-align:left;">Tgl Pasang</th>
                <th style="text-align:left;">Tgl Limit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($partLimitRows as $i => $row)
                @php $part = $row['part']; @endphp
                <tr style="{{ $i % 2 === 0 ? 'background:#fff' : 'background:#fef2f2' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $part->kendaraan?->merk ?? '-' }}</strong><br>
                        <span style="font-size:12px;color:#6b7280;">{{ $part->kendaraan?->nopol ?? '-' }}</span>
                    </td>
                    <td><strong>{{ $part->nama_part }}</strong></td>
                    <td>{{ $part->category?->nama ?? '—' }}</td>
                    <td>{{ $part->posisi ?: '—' }}</td>
                    <td>{{ $part->interval_nilai }} {{ $part->interval_satuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($part->tgl_pasang)->format('d M Y') }}</td>
                    <td style="color:#dc2626; font-weight:bold;">
                        {{ \Carbon\Carbon::parse($part->tanggal_limit)->format('d M Y') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:16px;">
        Segera lakukan penggantian part melalui modul Service History.
    </p>

    <p style="margin-top:20px;">
        <a href="{{ url('/admin/reminder-service') }}"
           style="background-color:#dc2626;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;display:inline-block;font-weight:bold;">
            Lihat Reminder Service
        </a>
    </p>

{{-- ============================================================
     TIPE: reminder — peringatan sebelum jatuh tempo
     ============================================================ --}}
@elseif ($tipe === 'reminder')

    <h2 style="color:#f59e0b;">⏰ Reminder Service Kendaraan</h2>

    <p>Jadwal service kendaraan berikut akan segera jatuh tempo.</p>

    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;">
        <tr><td><b>No Polisi</b></td><td>{{ $reminder->kendaraan->nopol ?? '-' }}</td></tr>
        <tr><td><b>Merk Kendaraan</b></td><td>{{ $reminder->kendaraan->merk ?? '-' }}</td></tr>
        <tr><td><b>Nama Reminder</b></td><td>{{ $reminder->nama_reminder }}</td></tr>
        <tr><td><b>Tanggal Mulai</b></td><td>{{ \Carbon\Carbon::parse($reminder->tanggal_mulai)->format('d-m-Y') }}</td></tr>
        <tr><td><b>Interval</b></td><td>{{ $reminder->interval_nilai }} {{ $reminder->interval_satuan }}</td></tr>
        <tr><td><b>Jatuh Tempo</b></td><td>{{ \Carbon\Carbon::parse($reminder->tanggal_jatuh_tempo)->format('d-m-Y') }}</td></tr>
        <tr><td><b>Sisa Waktu</b></td><td>{{ $sisaHari }} hari lagi</td></tr>
        @if ($reminder->keterangan)
            <tr><td><b>Keterangan</b></td><td>{{ $reminder->keterangan }}</td></tr>
        @endif
    </table>

    <p>Mohon segera melakukan pengecekan dan service sebelum tanggal jatuh tempo.</p>

    <p style="margin-top:20px;">
        <a href="{{ url('/admin/reminder-service') }}"
           style="background-color:#2563eb;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;display:inline-block;font-weight:bold;">
            Lihat Reminder Service
        </a>
    </p>

{{-- ============================================================
     TIPE: jatuh_tempo — sudah lewat
     ============================================================ --}}
@else

    <h2 style="color:red;">⚠ Reminder Service Kendaraan Jatuh Tempo</h2>

    <p>Jadwal service kendaraan berikut telah melewati tanggal jatuh tempo.</p>

    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;">
        <tr><td><b>No Polisi</b></td><td>{{ $reminder->kendaraan->nopol ?? '-' }}</td></tr>
        <tr><td><b>Merk Kendaraan</b></td><td>{{ $reminder->kendaraan->merk ?? '-' }}</td></tr>
        <tr><td><b>Nama Reminder</b></td><td>{{ $reminder->nama_reminder }}</td></tr>
        <tr><td><b>Tanggal Mulai</b></td><td>{{ \Carbon\Carbon::parse($reminder->tanggal_mulai)->format('d-m-Y') }}</td></tr>
        <tr><td><b>Interval</b></td><td>{{ $reminder->interval_nilai }} {{ $reminder->interval_satuan }}</td></tr>
        <tr><td><b>Jatuh Tempo</b></td><td>{{ \Carbon\Carbon::parse($reminder->tanggal_jatuh_tempo)->format('d-m-Y') }}</td></tr>
        <tr><td><b>Terlambat</b></td><td>{{ abs($sisaHari) }} hari</td></tr>
        @if ($reminder->keterangan)
            <tr><td><b>Keterangan</b></td><td>{{ $reminder->keterangan }}</td></tr>
        @endif
    </table>

    <p>Segera lakukan pengecekan dan service kendaraan tersebut.</p>

    <p style="margin-top:20px;">
        <a href="{{ url('/admin/reminder-service') }}"
           style="background-color:#dc2626;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;display:inline-block;font-weight:bold;">
            Lihat Reminder Service
        </a>
    </p>

@endif

<br>
<p style="color:#9ca3af; font-size:12px;">
    Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
</p>

</body>
</html>
