<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
body{font-family:Arial,sans-serif;font-size:11px;color:#333}
.header{display:flex;align-items:center;border-bottom:2px solid #2563eb;padding-bottom:10px;margin-bottom:15px}
.header img{height:50px;margin-right:15px}
.header h1{font-size:16px;font-weight:bold;color:#1e3a5f;margin:0}
.header p{font-size:10px;color:#666;margin:2px 0 0}
table{width:100%;border-collapse:collapse;margin-top:10px}
th{background:#1e3a5f;color:white;padding:6px 8px;text-align:left;font-size:10px}
td{padding:5px 8px;border-bottom:1px solid #e5e7eb;font-size:10px}
tr:nth-child(even) td{background:#f8fafc}
.badge{padding:2px 6px;border-radius:10px;font-size:9px;font-weight:bold}
.footer{margin-top:20px;font-size:9px;color:#9ca3af;text-align:right}
</style></head>
<body>
<div class="header">
    @if($logoSrc)<img src="{{ $logoSrc }}" alt="Logo">@endif
    <div><h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1><p>Laporan Project Planning &bull; Dicetak: {{ now()->format('d M Y H:i') }}</p></div>
</div>
<table>
    <thead><tr>
        <th>No</th><th>Kode Proyek</th><th>Tahapan</th><th>Tgl Mulai</th><th>Tgl Selesai</th><th>Durasi</th><th>PIC</th><th>Status</th>
    </tr></thead>
    <tbody>
        @forelse($data as $i => $d)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><b>{{ $d->kode_proyek }}</b></td>
            <td>{{ $d->tahapan }}</td>
            <td>{{ \Carbon\Carbon::parse($d->tgl_mulai)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($d->tgl_selesai)->format('d/m/Y') }}</td>
            <td>{{ $d->durasi }} hari</td>
            <td>{{ $d->pic }}</td>
            <td>{{ $d->status }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#9ca3af">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">Total: {{ $data->count() }} tahapan</div>
</body></html>
