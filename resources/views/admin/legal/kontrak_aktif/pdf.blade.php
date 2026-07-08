<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"><title>Kontrak Aktif</title>
    <style>
        body{font-family:sans-serif;font-size:10px;color:#222;margin:0;padding:20px;}
        .header{display:flex;align-items:center;gap:12px;border-bottom:2px solid #2563eb;padding-bottom:10px;margin-bottom:16px;}
        .header img{width:48px;height:48px;object-fit:contain;}
        .header-text h1{font-size:16px;font-weight:bold;color:#2563eb;margin:0;}
        .header-text p{font-size:10px;color:#666;margin:2px 0 0;}
        .meta{font-size:9px;color:#888;margin-bottom:12px;}
        table{width:100%;border-collapse:collapse;}
        thead tr{background:#2563eb;color:white;}
        thead th{padding:6px 8px;text-align:left;font-size:9px;font-weight:600;text-transform:uppercase;}
        tbody tr:nth-child(even){background:#f8fafc;}
        tbody td{padding:5px 8px;border-bottom:1px solid #e5e7eb;font-size:9px;}
        .badge{display:inline-block;padding:1px 6px;border-radius:8px;font-size:8px;font-weight:600;}
        .badge-green{background:#dcfce7;color:#16a34a;}.badge-yellow{background:#fef9c3;color:#ca8a04;}.badge-gray{background:#f3f4f6;color:#6b7280;}
        .footer{margin-top:16px;text-align:right;font-size:8px;color:#999;border-top:1px solid #e5e7eb;padding-top:8px;}
    </style>
</head>
<body>
    <div class="header">
        @if($logoSrc)<img src="{{ $logoSrc }}" alt="Logo">@endif
        <div class="header-text"><h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1><p>Laporan Kontrak Aktif</p></div>
    </div>
    <div class="meta">Dicetak: {{ now()->format('d M Y H:i') }} | Total Data: {{ $data->count() }}</div>
    <table>
        <thead><tr><th>No</th><th>Kode Kontrak</th><th>Mitra</th><th>Nilai (Rp)</th><th>Tgl Mulai</th><th>Tgl Selesai</th><th>PIC</th><th>Perpanjangan</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($data as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td><td>{{ $d->kode_kontrak }}</td><td>{{ $d->mitra }}</td>
                <td>{{ number_format($d->nilai,0,',','.') }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tgl_mulai)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tgl_selesai)->format('d/m/Y') }}</td>
                <td>{{ $d->pic }}</td>
                <td>{{ $d->perpanjangan ? 'Ya' : 'Tidak' }}</td>
                <td><span class="badge {{ $d->status==='Aktif'?'badge-green':($d->status==='Draft'?'badge-yellow':'badge-gray') }}">{{ $d->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">{{ $setting?->nama_perusahaan ?? 'APY Rent' }} &mdash; Laporan Kontrak Aktif</div>
</body>
</html>
