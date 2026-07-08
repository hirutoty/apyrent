<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Notaris</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
            padding: 24px;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }
        .header-text h1 {
            font-size: 15px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 2px;
        }
        .header-text p {
            font-size: 9px;
            color: #6b7280;
        }

        /* Meta info */
        .meta {
            font-size: 9px;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background-color: #2563eb;
            color: #ffffff;
        }
        thead th {
            padding: 7px 10px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        tbody tr:hover {
            background-color: #eff6ff;
        }
        tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }
        tbody td.no {
            color: #9ca3af;
            text-align: center;
            width: 28px;
        }
        tbody td.empty {
            text-align: center;
            color: #9ca3af;
            padding: 24px;
        }

        /* Footer */
        .footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        @if(!empty($logoSrc))
            <img src="{{ $logoSrc }}" alt="Logo">
        @endif
        <div class="header-text">
            <h1>{{ $setting?->nama_perusahaan ?? 'APY Rent' }}</h1>
            <p>Laporan Daftar Notaris</p>
        </div>
    </div>

    {{-- META --}}
    <div class="meta">
        Dicetak: {{ now()->format('d M Y, H:i') }} WIB &nbsp;&bull;&nbsp; Total Data: {{ count($data) }}
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th style="width:28px">No</th>
                <th>Nama Kantor</th>
                <th>Layanan</th>
                <th>Kontak</th>
                <th>Email</th>
                <th style="width:90px">Terakhir Dipakai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $d)
            <tr>
                <td class="no">{{ $i + 1 }}</td>
                <td><strong>{{ $d->nama_kantor }}</strong></td>
                <td>{{ $d->layanan }}</td>
                <td>{{ $d->kontak }}</td>
                <td>{{ $d->email }}</td>
                <td>
                    @if($d->terakhir_dipakai)
                        {{ \Carbon\Carbon::parse($d->terakhir_dipakai)->format('d/m/Y') }}
                    @else
                        <span style="color:#9ca3af">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty">Tidak ada data notaris</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <span>{{ $setting?->nama_perusahaan ?? 'APY Rent' }} &mdash; Daftar Notaris</span>
        <span>Halaman 1</span>
    </div>

</body>
</html>
