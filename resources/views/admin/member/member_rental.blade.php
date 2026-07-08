@extends('admin.layouts.app')

@section('title', 'Member Rental Kendaraan')

@section('content')
<body class="bg-gray-100">

    <div class="p-6">

        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Sewa Kendaraan</h1>

            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded">
                + Tambah
            </button>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded overflow-x-auto">

            <table class="min-w-full text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2">Member</th>
                        <th class="p-2">Kendaraan</th>
                        <th class="p-2">Sewa</th>
                        <th class="p-2">Kembali</th>
                        <th class="p-2">Biaya/Hari</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $d)
                        <tr class="border-b">

                            <td class="p-2">{{ $d->member->nama_member ?? '-' }}</td>
                            <td class="p-2">{{ $d->kendaraan->nopol ?? '-' }}</td>
                            <td class="p-2">{{ $d->tanggal_sewa }}</td>
                            <td class="p-2">{{ $d->tanggal_kembali }}</td>
                            <td class="p-2">
                                Rp {{ number_format($d->total_biaya) }}
                            </td>

                            <td class="p-2">
                                @if ($d->status_sewa == 'aktif')
                                    <span class="text-green-600">Aktif</span>
                                @else
                                    <span class="text-red-600">Selesai</span>
                                @endif
                            </td>

                            <td class="p-2 space-x-2">

                                <button class="btn-edit bg-yellow-500 text-white px-2 py-1 rounded"
                                    data-id="{{ $d->id }}" data-member_id="{{ $d->member_id }}"
                                    data-kendaraan_id="{{ $d->kendaraan_id }}"
                                    data-tanggal_sewa="{{ $d->tanggal_sewa }}"
                                    data-tanggal_kembali="{{ $d->tanggal_kembali }}"
                                    data-biaya_sewa="{{ $d->biaya_sewa }}">
                                    Edit
                                </button>

                                <form action="/admin/member/member_rental/{{ $d->id }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button class="bg-red-500 text-white px-2 py-1 rounded">
                                        Hapus
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
            <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>

        </div>
    </div>

    <!-- MODAL -->
    <div id="modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white p-6 rounded w-96">

            <form id="form" method="POST">
                @csrf

                <select name="member_id" id="member_id" class="border p-2 w-full mb-2">
                    @foreach ($member as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_member }}</option>
                    @endforeach
                </select>

                <select name="kendaraan_id" id="kendaraan_id" class="border p-2 w-full mb-2">
                    @foreach ($kendaraan as $k)
                        <option value="{{ $k->id }}">{{ $k->nopol }}</option>
                    @endforeach
                </select>

                <input type="date" name="tanggal_sewa" id="tanggal_sewa" class="border p-2 w-full mb-2">
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="border p-2 w-full mb-2">

                <input type="number" name="biaya_sewa" id="biaya_sewa" class="border p-2 w-full mb-2"
                    placeholder="Biaya / Hari">

                <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                    Simpan
                </button>

            </form>

            <button onclick="closeModal()" class="mt-3 text-red-500">Tutup</button>

        </div>

    </div>

    <script>
       function openModal() {
    document.getElementById('modal').classList.remove('hidden');
    }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {

        document.getElementById('form').action =
            '/admin/member/member_rental/' + this.dataset.id;

        // ubah method jadi PUT
        if (!document.getElementById('method-put')) {
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            method.id = 'method-put';
            document.getElementById('form').appendChild(method);
        }

        document.getElementById('member_id').value = this.dataset.member_id;
        document.getElementById('kendaraan_id').value = this.dataset.kendaraan_id;
        document.getElementById('tanggal_sewa').value = this.dataset.tanggal_sewa;
        document.getElementById('tanggal_kembali').value = this.dataset.tanggal_kembali;
        document.getElementById('biaya_sewa').value = this.dataset.biaya_sewa;

        openModal();
    });
});
    </script>

</body>

@endsection