<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Data Ads Integration</title>
<style>body{font-family:sans-serif;font-size:9px;color:#222;margin:0;padding:20px}.header{display:flex;align-items:center;gap:12px;border-bottom:2px solid #2563eb;padding-bottom:10px;margin-bottom:16px}.header img{width:48px;height:48px;object-fit:contain}.header-text h1{font-size:15px;font-weight:bold;color:#2563eb;margin:0}.header-text p{font-size:9px;color:#666;margin:2px 0 0}.meta{font-size:8px;color:#888;margin-bottom:12px}table{width:100%;border-collapse:collapse}thead tr{background:#2563eb;color:white}thead th{padding:5px 6px;text-align:left;font-size:8px;font-weight:600;text-transform:uppercase}tbody tr:nth-child(even){background:#f8fafc}tbody td{padding:4px 6px;border-bottom:1px solid #e5e7eb;font-size:8px}.footer{margin-top:16px;text-align:right;font-size:8px;color:#999;border-top:1px solid #e5e7eb;padding-top:8px}</style>
</head>
<body>
<div class="header">@if($logoSrc)<img src="{{ $logoSrc }}" alt="Logo">@endif<div class="header-text"><h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1><p>Laporan Ads Integration</p></div></div>
<div class="meta">Dicetak: {{ now()->format('d M Y H:i') }} | Total: {{ $data->count() }}</div>
<table>
    <thead><tr><th>No</th><th>ID</th><th>Nama Iklan</th><th>Platform</th><th>Tgl Aktif</th><th>Budget/Hari</th><th>Klik</th><th>Konversi</th><th>Biaya Total</th><th>Penjualan</th><th>ROI</th></tr></thead>
    <tbody>
        @forelse($data as $i => $d)
        <tr><td>{{ $i+1 }}</td><td>{{ $d->id_iklan }}</td><td>{{ $d->nama_iklan }}</td><td>{{ $d->platform }}</td>
        <td>{{ \Carbon\Carbon::parse($d->tanggal_aktif)->format('d/m/Y') }}</td>
        <td>{{ number_format($d->budget_harian,0,',','.') }}</td>
        <td>{{ number_format($d->klik) }}</td><td>{{ number_format($d->konversi) }}</td>
        <td>{{ number_format($d->biaya_total,0,',','.') }}</td><td>{{ number_format($d->penjualan,0,',','.') }}</td>
        <td>{{ $d->roi }}</td></tr>
        @empty<tr><td colspan="11" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>@endforelse
    </tbody>
</table>
<div class="footer">{{ $setting?->nama_perusahaan ?? 'APY Rent' }} &mdash; Laporan Ads Integration</div>
</body></html>
