<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 15px; }
        .header img { height: 50px; margin-right: 15px; }
        .header h1 { font-size: 16px; font-weight: bold; color: #1e3a5f; margin: 0; }
        .header p { font-size: 10px; color: #666; margin: 2px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1e3a5f; color: white; padding: 6px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoSrc)
        <img src="{{ $logoSrc }}" alt="Logo">
        @endif
        <div>
            <h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1>
            <p>Laporan Induk Proyek &bull; Dicetak: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Proyek</th>
                <th>Jenis</th>
                <th>PIC</th>
                <th>Mulai</th>
                <th>Target Selesai</th>
                <th>Progres</th>
                <th>Nilai Proyek</th>
                <th>Lokasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td><b>{{ $d->kode }}</b></td>
                <td>{{ $d->nama_proyek }}</td>
                <td>{{ $d->jenis }}</td>
                <td>{{ $d->pic }}</td>
                <td>{{ \Carbon\Carbon::parse($d->mulai)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($d->target_selesai)->format('d/m/Y') }}</td>
                <td>{{ $d->progres }}</td>
                <td>Rp {{ number_format($d->nilai_proyek,0,',','.') }}</td>
                <td>{{ $d->lokasi }}</td>
                <td>
                    @php $sc = match($d->status) { 'Berjalan'=>'badge-green','Approved'=>'badge-blue','Plan'=>'badge-yellow', default=>'badge-gray' }; @endphp
                    <span class="badge {{ $sc }}">{{ $d->status }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center;color:#9ca3af;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Total: {{ $data->count() }} proyek</div>
</body>
</html>
