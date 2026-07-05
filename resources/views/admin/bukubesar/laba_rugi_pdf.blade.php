<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { padding: 18px 28px 14px; border-bottom: 2px solid #e0e0e0; }
        .header-table { width: 100%; }
        .logo { height: 60px; width: auto; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e56b0; margin-bottom: 4px; }
        .company-info { font-size: 10px; line-height: 1.6; color: #555; }
        .report-title { text-align: right; }
        .report-title h1 { font-size: 26px; color: #1e56b0; letter-spacing: 2px; }
        .report-title p { font-size: 11px; color: #666; }
        .info-box { margin: 15px 28px; padding: 12px; border: 1px solid #d0d7e3; background: #f8fafc; border-radius: 4px; }
        .info-box p { margin: 4px 0; }
        .table-wrap { padding: 0 28px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table thead tr { background: #1e56b0; }
        .main-table th { color: #fff; padding: 9px; border: 1px solid #d0d7e3; text-align: center; font-size: 11px; }
        .main-table td { border: 1px solid #d0d7e3; padding: 7px; font-size: 10px; text-align: right; }
        .main-table td.center { text-align: center; }
        .row-laba td { background: #dcfce7; font-weight: bold; color: #166534; }
        .row-rugi td { background: #fee2e2; font-weight: bold; color: #991b1b; }
        .footer { margin-top: 25px; padding: 14px 28px; border-top: 1px solid #dbeafe; text-align: center; color: #1e56b0; font-size: 10px; line-height: 1.7; }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td width="15%">
                @if($setting?->logo)
                    <img src="{{ public_path($setting->logo) }}" class="logo">
                @endif
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
                    <h1>LABA RUGI</h1>
                    <p>Laporan Laba Rugi Perusahaan</p>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="info-box">
    <p><strong>Tanggal Cetak :</strong> {{ now()->format('d M Y H:i') }}</p>
</div>

<div class="table-wrap">
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Total Beban (Rp)</th>
                <th width="25%">Total Pendapatan (Rp)</th>
                <th width="22%">Laba Kotor (Rp)</th>
                <th width="23%">Laba Bersih (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="{{ $labaBersih < 0 ? 'row-rugi' : 'row-laba' }}">
                <td class="center">1</td>
                <td>Rp {{ number_format($totalBeban, 2, ',', '.') }}</td>
                <td>Rp {{ number_format($pendapatan, 2, ',', '.') }}</td>
                <td>Rp {{ number_format($labaKotor, 2, ',', '.') }}</td>
                <td>Rp {{ number_format($labaBersih, 2, ',', '.') }}</td>
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
