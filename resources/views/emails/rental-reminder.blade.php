<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reminder Rental</title>
</head>

<body style="font-family: Arial; line-height:1.6;">

    @if ($tipe == 'reminder')

        <h2 style="color:#f59e0b;">⏰ Reminder Rental Kendaraan</h2>

        <p>Rental kendaraan akan segera berakhir.</p>

        <table border="1" cellpadding="6">

            <tr>
                <td>No Polisi</td>
                <td>{{ $rental->kendaraan->nopol }}</td>
            </tr>

            <tr>
                <td>Merk</td>
                <td>{{ $rental->kendaraan->merk }}</td>
            </tr>

            <tr>
                <td>Member</td>
                <td>{{ $rental->member->nama_member }}</td>
            </tr>

            <tr>
                <td>Jenis Member</td>
                <td>{{ $rental->member->jenis_member ?? '-' }}</td>
            </tr>

            <tr>
                <td>Tujuan</td>
                <td>{{ $rental->tujuan ?? '-' }}</td>
            </tr>

            <tr>
                <td>Jenis Rental</td>
                <td>
                    @if ($rental->durasi_hari)
                        Harian
                    @elseif($rental->durasi_bulan)
                        Bulanan
                    @elseif($rental->durasi_tahun)
                        Tahunan
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <td>Durasi Sewa</td>
                <td>
                    {{ $rental->durasi_hari ?? ($rental->durasi_bulan ?? ($rental->durasi_tahun ?? '-')) }}
                </td>
            </tr>

            <tr>
                <td>Total Biaya</td>
                <td>Rp {{ number_format($rental->total_biaya, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td>Jenis Pembayaran</td>
                <td>{{ strtoupper($rental->jenis_pembayaran ?? '-') }}</td>
            </tr>

            <tr>
                <td>Status Pembayaran</td>
                <td>
                    @if ($rental->bukti_lunas || $rental->bukti_pelunasan)
                        LUNAS
                    @else
                        BELUM LUNAS
                    @endif
                </td>
            </tr>

            <tr>
                <td>Check-in</td>
                <td>{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td>Check-out</td>
                <td>{{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td>Sisa Hari</td>
                <td>{{ $hari }} hari</td>
            </tr>

        </table>
    @else
        <h2 style="color:red;">⚠ Rental Terlambat</h2>

        <p>Rental sudah melewati batas waktu.</p>

        <table border="1" cellpadding="6">

            <tr>
                <td>No Polisi</td>
                <td>{{ $rental->kendaraan->nopol }}</td>
            </tr>

            <tr>
                <td>Merk</td>
                <td>{{ $rental->kendaraan->merk }}</td>
            </tr>

            <tr>
                <td>Member</td>
                <td>{{ $rental->member->nama_member }}</td>
            </tr>

            <tr>
                <td>Jenis Member</td>
                <td>{{ $rental->member->jenis_member ?? '-' }}</td>
            </tr>


            <tr>
                <td>Tujuan</td>
                <td>{{ $rental->tujuan }}</td>
            </tr>


            <tr>
                <td>Nama Driver</td>
                <td>{{ $rental->nama_driver ?? '-' }}</td>
            </tr>

            <tr>
                <td>Kontak Driver</td>
                <td>{{ $rental->kontak_driver ?? '-' }}</td>
            </tr>

            <tr>
                <td>Biaya Driver</td>
                <td>Rp {{ number_format($rental->biaya_driver ?? 0, 0, ',', '.') }}</td>
            </tr>


            <tr>
                <td>Total Biaya</td>
                <td>Rp {{ number_format($rental->total_biaya, 0, ',', '.') }}</td>
            </tr>



            <tr>
                <td>Jenis Pembayaran</td>
                <td>{{ strtoupper($rental->jenis_pembayaran ?? '-') }}</td>
            </tr>

            
                <tr>
                    <td>Nominal DP</td>
                    <td>Rp {{ number_format($rental->nominal_dp, 0, ',', '.') }}</td>
                </tr>

                <tr>
                    <td>Pelunasan</td>
                    <td>
                        Rp {{ number_format($rental->total_biaya - ($rental->nominal_dp ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
            

            <tr>
                <td>Status Pembayaran</td>
                <td>
                    @if ($rental->bukti_lunas || $rental->bukti_pelunasan)
                        LUNAS
                    @else
                        BELUM LUNAS
                    @endif
                </td>
            </tr>

            <tr>
                <td>Durasi Sewa</td>
                <td>
                    {{ $rental->durasi_hari ?? ($rental->durasi_bulan ?? ($rental->durasi_tahun ?? '-')) }}
                </td>
            </tr>

            <tr>
                <td>Jenis Rental</td>
                <td>
                    @if ($rental->durasi_hari)
                        Harian
                    @elseif($rental->durasi_bulan)
                        Bulanan
                    @elseif($rental->durasi_tahun)
                        Tahunan
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <td>Check-in</td>
                <td>{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td>Check-out</td>
                <td>{{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d-m-Y') }}</td>
            </tr>



            <tr>
                <td>Sisa Hari</td>
                <td>{{ $hari }} hari</td>
            </tr>

        </table>

    @endif

    <p style="margin-top:20px;">
        Email ini dikirim secara otomatis oleh Sistem Manajemen Rental
    </p>

</body>

</html>
