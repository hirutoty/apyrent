<!DOCTYPE html>
<html>

<body>
    <h2>Reminder Pembayaran</h2>

    <p>Yth. {{ $aging->member->nama_member }}</p>

    <p>
        Invoice <strong>{{ $aging->invoice->invoice_no }}</strong>
        akan jatuh tempo pada
        <strong>{{ \Carbon\Carbon::parse($aging->jatuh_tempo)->format('d M Y') }}</strong>.
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <td>No Invoice</td>
            <td>{{ $aging->invoice->invoice_no }}</td>
        </tr>

        <tr>
            <td>Total</td>
            <td>Rp {{ number_format($aging->total,0,',','.') }}</td>
        </tr>

        <tr>
            <td>Jatuh Tempo</td>
            <td>{{ \Carbon\Carbon::parse($aging->jatuh_tempo)->format('d M Y') }}</td>
        </tr>
    </table>

    <br>

    <p>
        Mohon segera melakukan pembayaran sebelum tanggal jatuh tempo.
    </p>

</body>

</html>