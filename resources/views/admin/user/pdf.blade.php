<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data User</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            padding: 18px 28px 14px;
            border-bottom: 2px solid #e0e0e0;
        }

        .header-table {
            width: 100%;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e56b0;
            margin-bottom: 4px;
        }

        .company-info {
            font-size: 10px;
            line-height: 1.6;
            color: #555;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 26px;
            color: #1e56b0;
            letter-spacing: 2px;
        }

        .report-title p {
            font-size: 11px;
            color: #666;
        }

        .info-box {
            margin: 15px 28px;
            padding: 12px;
            border: 1px solid #d0d7e3;
            background: #f8fafc;
            border-radius: 4px;
        }

        .info-box p {
            margin: 4px 0;
        }

        .table-wrap {
            padding: 0 28px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table thead tr {
            background: #1e56b0;
        }

        .main-table th {
            color: #fff;
            padding: 9px;
            border: 1px solid #d0d7e3;
            text-align: center;
            font-size: 11px;
        }

        .main-table td {
            border: 1px solid #d0d7e3;
            padding: 7px;
            font-size: 10px;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .row-subtotal td {
            background: #eff6ff;
            font-weight: bold;
        }

        .row-total td {
            background: #1e56b0;
            color: #fff;
            font-weight: bold;
        }

        .note-box {
            margin: 18px 28px;
            border: 1px solid #d0d7e3;
            border-radius: 5px;
            overflow: hidden;
            background: #fff;
        }

        .note-title {
            background: #1e56b0;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            padding: 8px 12px;
        }

        .note-content {
            padding: 12px;
            font-size: 10px;
            line-height: 1.8;
            color: #444;
            text-align: justify;
        }

        .footer {
            margin-top: 25px;
            padding: 14px 28px;
            border-top: 1px solid #dbeafe;
            text-align: center;
            color: #1e56b0;
            font-size: 10px;
            line-height: 1.7;
        }
    </style>
</head>

<body>

<div class="header">
        <table class="header-table">
            <tr>

                <td width="15%">
                    @if ($setting?->logo)
                       <img src="{{ public_path($setting->logo) }}" class="logo">
                    @endif
                </td>

                <td width="55%">
                    <div class="company-name">
                        {{ $setting?->nama_perusahaan }}
                    </div>

                    <div class="company-info">
                        {{ $setting?->alamat }}
                        <br>
                        Telp : {{ $setting?->telepon }}
                        <br>
                        Email : {{ $setting?->email }}

                        @if ($setting?->website)
                            <br>
                            Website : {{ $setting->website }}
                        @endif
                    </div>
                </td>

                <td width="30%">
                    <div class="report-title">
                        <h1>User</h1>
                        <p>Laporan User Account Perusahaan</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>


{{-- INFO BOX --}}
<div class="info-box">

    <p>
        <strong>Tanggal Cetak :</strong>
        {{ now()->format('d M Y H:i') }}
    </p>

    @if(!empty($filter))
        <p>
            <strong>Filter :</strong>
            {{ $filter }}
        </p>
    @endif

    <p>
        <strong>Total User :</strong>
        {{ $data->count() }}
    </p>

</div>

{{-- TABEL --}}
<div class="table-wrap">

    <table class="main-table">

        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="18%">Nama</th>
                <th width="15%">Username</th>
                <th width="22%">Email</th>
                <th width="15%">No Telp</th>
                <th width="12%">Role</th>
                <th width="13%">Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse($data as $i => $d)

                <tr>

                    <td class="text-center">
                        {{ $i + 1 }}
                    </td>

                    <td>
                        {{ $d->name }}
                    </td>

                    <td>
                        {{ $d->username }}
                    </td>

                    <td>
                        {{ $d->email }}
                    </td>

                    <td>
                        {{ $d->no_telp }}
                    </td>

                    <td class="text-center">
                        {{ strtoupper($d->role) }}
                    </td>

                    <td class="text-center">
                        {{ strtoupper($d->status) }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="text-center">
                        Tidak ada data user.
                    </td>
                </tr>

            @endforelse

            <tr class="row-subtotal">

                <td colspan="6" class="text-right">
                    TOTAL USER
                </td>

                <td class="text-center">
                    {{ $data->count() }} User
                </td>

            </tr>

        </tbody>

    </table>

</div>

{{-- KETERANGAN --}}
{{-- <div class="note-box">

    <div class="note-title">
        KETERANGAN DOKUMEN
    </div>

    <div class="note-content">
        Laporan ini merupakan daftar pengguna sistem
        {{ $setting?->nama_perusahaan }}.
        Informasi yang ditampilkan meliputi nama pengguna,
        username, email, nomor telepon, peran (role),
        dan status akun sebagai dasar pengelolaan akses
        serta monitoring pengguna aplikasi.
    </div>

</div> --}}

<div class="footer">

        <strong>{{ $setting?->nama_perusahaan }}</strong>
        <br>

        {{ $setting?->alamat }}
        <br>

        Telp : {{ $setting?->telepon }}
        |
        Email : {{ $setting?->email }}

        @if ($setting?->website)
            <br>
            {{ $setting->website }}
        @endif

    </div>

</body>
</html>