<h2>⚠ Overservice Alert</h2>

<p>Kendaraan berikut memiliki part dengan biaya melebihi batas limit kategori:</p>

<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <tr>
        <td><strong>Kendaraan</strong></td>
        <td>{{ $kendaraan->merk }} {{ $kendaraan->model ?? '' }} — {{ $kendaraan->nopol }}</td>
    </tr>
</table>

<br>

<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <thead>
        <tr style="background:#f3f4f6;">
            <th style="text-align:left;">Nama Part</th>
            <th style="text-align:left;">Kategori</th>
            <th style="text-align:left;">Tanggal Pasang</th>
            <th style="text-align:right;">Biaya</th>
            <th style="text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parts as $part)
        <tr>
            <td>{{ $part->nama_part }}</td>
            <td>{{ $part->category?->nama ?? '-' }}</td>
            <td>{{ $part->tgl_pasang ? \Carbon\Carbon::parse($part->tgl_pasang)->format('d/m/Y') : '-' }}</td>
            <td style="text-align:right;">Rp {{ number_format($part->biaya, 0, ',', '.') }}</td>
            <td style="text-align:center;">
                <span style="
                    background:#fef2f2;
                    color:#dc2626;
                    padding:2px 8px;
                    border-radius:9999px;
                    font-size:12px;
                    font-weight:bold;
                ">Overservice</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top:16px;">
    Segera lakukan pengecekan dan tindak lanjut.
</p>

<p style="margin-top:20px;">
    <a href="https://apy.creativegamastudio.com/admin/service-history"
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
        Lihat Service
    </a>
</p>

<br>

<p>
    Email ini dikirim secara otomatis oleh Sistem Manajemen Rental.
</p>
