<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Penawaran Sales</title>
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
        .badge-gray   { background: #f3f4f6; color: #6b7280; }
        .badge-yellow { background: #fef9c3; color: #ca8a04; }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .footer { margin-top: 16px; text-align: right; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo">
        @endif
        <div class="header-text">
            <h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1>
            <p>Laporan Penawaran Sales</p>
        </div>
    </div>
    <div class="meta">Dicetak: {{ now()->format('d M Y H:i') }} | Total Data: {{ $data->count() }}</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Quotation</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Produk/Jasa</th>
                <th>Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Total Harga</th>
                <th>Valid Sampai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $d->no_quotation }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $d->pelanggan }}</td>
                <td>{{ $d->produk_jasa }}</td>
                <td>{{ $d->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($d->total_harga, 0, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($d->valid_sampai)->format('d/m/Y') }}</td>
                <td>
                    @php
                        $bc = match($d->status) { 'Draft'=>'badge-gray','Terkirim'=>'badge-yellow','Disetujui'=>'badge-green','Ditolak'=>'badge-red', default=>'badge-gray' };
                    @endphp
                    <span class="badge {{ $bc }}">{{ $d->status }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">{{ $setting?->nama_perusahaan ?? 'APY Rent' }} &mdash; Laporan Penawaran Sales</div>
</body>
</html>
