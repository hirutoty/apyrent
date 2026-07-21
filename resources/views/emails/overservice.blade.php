<h2>⚠ Overservice Alert</h2>

<p>Kendaraan mengalami overservice!</p>

<table border="1" cellpadding="6">
    <tr>
        <td>Kendaraan</td>
        <td>{{ $service->kendaraan->merk }} {{ $service->kendaraan->nopol }}</td>
    </tr>

    <tr>
        <td>Nopol</td>
        <td>{{ $service->kendaraan->nopol }}</td>
    </tr>

    <tr>
        <td>Tanggal Service</td>
        <td>{{ $service->tanggal_service }}</td>
    </tr>

    <tr>
        <td>Total Biaya</td>
        <td>Rp {{ number_format($service->total_biaya, 0, ',', '.') }}</td>
    </tr>

    <tr>
        <td>Limit Bulan</td>
        <td>Rp {{ number_format($service->kendaraan->limit_biaya_bulanan_service, 0, ',', '.') }}</td>
    </tr>

    <tr>
        <td>Status</td>
        <td>{{ $service->status_pengeluaran }}</td>
    </tr>


</table>

<p>
    Segera lakukan pengecekan
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
