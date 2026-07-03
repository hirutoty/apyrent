<h2>Data STNK Kendaraan</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Nopol</th>
            <th>Pemilik</th>
            <th>Model</th>
            <th>Masa Berlaku</th>
            <th>Biaya</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->nopol }}</td>
                <td>{{ $item->nama_pemilik }}</td>
                <td>{{ $item->jenis_model }}</td>
                <td>{{ $item->masa_berlaku }}</td>
                <td>{{ number_format($item->biaya) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>