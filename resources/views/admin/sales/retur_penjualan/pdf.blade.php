<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Retur Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #222; margin: 0; padding: 20px; }
        .header { display: flex; align-items: center; gap: 12px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 16px; }
        .header img { width: 48px; height: 48px; object-fit: contain; }
        .header-text h1 { font-size: 16px; font-weight: bold; color: #2563eb; margin: 0; }
        .header-text p { font-size: 10px; color: #666; margin: 2px 0 0; }
        .meta { font-size: 9px; color: #888; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #2563eb; color: white; }
        thead th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: 600; }
        .badge-yellow { background: #fef9c3; color: #ca8a04; }
        .badge-indigo { background: #e0e7ff; color: #4338ca; }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-gray   { background: #f3f4f6; color: #6b7280; }
        .footer { margin-top: 16px; text-align: right; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoSrc) <img src="{{ $logoSrc }}" alt="Logo"> @endif
        <div class="header-text">
            <h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1>
            <p>Laporan Retur Penjualan</p>
        </div>
    </div>
    <div class="meta">Dicetak: {{ now()->format('d M Y H:i') }} | Total Data: {{ $data->count() }}</div>
    <table>
        <thead>
            <tr>
                <th>No</th><th>No. Retur</th><th>Tanggal</th><th>No. Order</th>
                <th>Pelanggan</th><th>Produk</th><th>Qty</th>
                <th class="text-right">Nilai Retur</th><th>Alasan</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $d->no_retur }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $d->no_order }}</td>
                <td>{{ $d->pelanggan }}</td>
                <td>{{ $d->produk }}</td>
                <td>{{ $d->qty }}</td>
                <td class="text-right">Rp {{ number_format($d->nilai_retur, 0, ',', '.') }}</td>
                <td>{{ \Illuminate\Support\Str::limit($d->alasan, 30) }}</td>
                <td>
                    @php $bc = match($d->status) { 'Menunggu'=>'badge-yellow','Diproses'=>'badge-indigo','Selesai'=>'badge-green', default=>'badge-gray' }; @endphp
                    <span class="badge {{ $bc }}">{{ $d->status }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">{{ $setting?->nama_perusahaan ?? 'APY Rent' }} &mdash; Laporan Retur Penjualan</div>
</body>
</html>
