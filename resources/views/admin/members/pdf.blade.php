<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Member</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#222; }
        .header { padding:18px 28px 14px; border-bottom:2px solid #e0e0e0; }
        .header-table { width:100%; }
        .logo { height:60px; width:auto; }
        .company-name { font-size:18px; font-weight:bold; color:#1e56b0; margin-bottom:4px; }
        .company-info { font-size:10px; line-height:1.6; color:#555; }
        .report-title { text-align:right; }
        .report-title h1 { font-size:26px; color:#1e56b0; letter-spacing:2px; }
        .report-title p { font-size:11px; color:#666; }
        .info-box { margin:15px 28px; padding:12px; border:1px solid #d0d7e3; background:#f8fafc; border-radius:4px; }
        .info-box p { margin:4px 0; }
        .table-wrap { padding:0 28px; }
        .main-table { width:100%; border-collapse:collapse; }
        .main-table thead tr { background:#1e56b0; }
        .main-table th { color:#fff; padding:9px; border:1px solid #d0d7e3; text-align:center; font-size:11px; }
        .main-table td { border:1px solid #d0d7e3; padding:7px; font-size:10px; }
        .main-table tbody tr:nth-child(even) { background:#f8fafc; }
        .text-center { text-align:center; }
        .text-right  { text-align:right; }
        .row-subtotal td { background:#eff6ff; font-weight:bold; }
        .footer { margin-top:25px; padding:14px 28px; border-top:1px solid #dbeafe; text-align:center; color:#1e56b0; font-size:10px; line-height:1.7; }
        .badge { display:inline-block; padding:2px 6px; border-radius:8px; font-size:9px; font-weight:bold; }
        .badge-blue  { background:#dbeafe; color:#1d4ed8; }
        .badge-green { background:#dcfce7; color:#15803d; }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td width="15%">
                    @if($logoSrc)<img src="{{ $logoSrc }}" class="logo">@endif
                </td>
                <td width="55%">
                    <div class="company-name">{{ $setting?->nama_perusahaan }}</div>
                    <div class="company-info">
                        {{ $setting?->alamat }}<br>
                        Telp : {{ $setting?->telepon }}<br>
                        Email : {{ $setting?->email }}
                        @if($setting?->website)<br>Website : {{ $setting->website }}@endif
                    </div>
                </td>
                <td width="30%">
                    <div class="report-title">
                        <h1>Member</h1>
                        <p>Laporan Data Mitra Pemilik Kendaraan</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <p><strong>Tanggal Cetak :</strong> {{ now()->format('d M Y H:i') }}</p>
        @if(request('search'))
            <p><strong>Filter :</strong> {{ request('search') }}</p>
        @endif
        <p><strong>Total Member :</strong> {{ $data->count() }}</p>
    </div>

    <div class="table-wrap">
        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="22%">Nama</th>
                    <th width="15%">Kontak</th>
                    <th width="18%">Email</th>
                    <th width="12%">Jenis</th>
                    <th width="8%">Kendaraan</th>
                    <th width="20%">Alamat</th>
                    <th width="18%">Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $d)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td><strong>{{ $d->nama }}</strong></td>
                        <td>{{ $d->kontak ?? '-' }}</td>
                        <td>{{ $d->email ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $d->jenis_member=='perorangan' ? 'badge-blue' : 'badge-green' }}">
                                {{ ucfirst($d->jenis_member) }}
                            </span>
                        </td>
                        <td class="text-center">{{ $d->kendaraans_count }} unit</td>
                        <td>{{ $d->alamat ?? '-' }}</td>
                        <td class="text-center">
                            {{ $d->file_stnk ? 'STNK ✓' : '' }}
                            {{ $d->file_attachment ? ' | Att ✓' : '' }}
                            {{ $d->file_kontrak ? ' | Kontrak ✓' : '' }}
                            {{ (!$d->file_stnk && !$d->file_attachment && !$d->file_kontrak) ? '-' : '' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">Tidak ada data member.</td></tr>
                @endforelse
                <tr class="row-subtotal">
                    <td colspan="5" class="text-right">TOTAL MEMBER</td>
                    <td class="text-center" colspan="3">{{ $data->count() }} Member</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <strong>{{ $setting?->nama_perusahaan }}</strong><br>
        {{ $setting?->alamat }}<br>
        Telp : {{ $setting?->telepon }} | Email : {{ $setting?->email }}
        @if($setting?->website)<br>{{ $setting->website }}@endif
    </div>

</body>
</html>
