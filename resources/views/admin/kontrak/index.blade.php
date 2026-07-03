@extends('admin.layouts.app')

@section('title', 'Data Kontrak Kendaraan')

@section('content')
    <div class="p-4">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold">Data Kontrak</h1>
                <p class="text-sm text-gray-500">Kelola data kontrak penawaran</p>
            </div>

            <button onclick="openModal('modalCreate')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                + Tambah Kontrak
            </button>
        </div>

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @php
                    $navItems = [
                        ['label' => 'Summary', 'url' => '/admin/summary', 'icon' => 'bi bi-bar-chart-line'],
                        ['label' => 'Penawaran', 'url' => '/admin/penawaran', 'icon' => 'bi bi-file-earmark-richtext'],
                        ['label' => 'Kontrak', 'url' => '/admin/kontrak', 'icon' => 'bi bi-file-earmark-lock'],
                        ['label' => 'Invoice', 'url' => '/admin/invoices', 'icon' => 'bi bi-receipt-cutoff'],
                        ['label' => 'Payments', 'url' => '/admin/payments', 'icon' => 'bi bi-credit-card-2-front'],
                        ['label' => 'Reminders', 'url' => '/admin/reminders', 'icon' => 'bi bi-bell'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php
                        $isActive =
                            request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*');
                    @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                            {{ $isActive
                                ? 'border-blue-600 text-blue-600 bg-blue-50/50'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- SEARCH --}}
        <form method="GET" class="mb-3">
            <input type="text" name="search" class="border rounded-lg px-3 py-2 text-sm w-64"
                placeholder="Cari no kontrak / pihak..." value="{{ request('search') }}">
        </form>

        {{-- TABLE --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">No Kontrak</th>
                        <th class="p-3">Penawaran</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Perjanjan</th>
                        <th class="p-3">Pihak 1</th>
                        <th class="p-3">Pihak 2</th>
                        <th class="p-3">File Kontrak</th>
                        <th class="p-3">FIle Persyaratan 2</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($kontraks as $no => $k)


                        <tr class="border-b">

                            <td class="p-3">{{ $no + 1 }}</td>

                            <td class="p-3 font-semibold">
                                {{ $k->no_kontrak }}
                            </td>

                            <td class="p-3">
                                {{ $k->penawaran->no_penawaran ?? '-' }}
                            </td>

                            <td class="p-3">
                                {{ $k->tanggal_kontrak?->format('d-m-Y') }}
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-1">

                                    <span {{ $k->perjanjian_pembayaran?->format('d-m-Y') }} </span>

                                        @if ($k->showReminder)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                Terlambat {{ abs($k->sisaHari) }} hari
                                            </span>
                                        @elseif ($k->isExpired)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit animate-pulse">
                                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>

                                                @if ($k->sisaHari == 0)
                                                    Jatuh Tempo Hari Ini
                                                @elseif ($k->sisaHari == 1)
                                                    Jatuh Tempo Besok
                                                @else
                                                    Jatuh Tempo {{ $k->sisaHari }} hari lagi
                                                @endif

                                            </span>
                                        @endif

                                </div>
                            </td>

                            <td class="p-3">
                                {{ $k->pihak_pertama }}
                            </td>

                            <td class="p-3">
                                {{ $k->pihak_kedua }}
                            </td>

                            <td class="p-3">
                                @if ($k->file_kontrak)
                                    <a href="{{ asset($k->file_kontrak) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        Lihat File
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="p-3">
                                @if ($k->file_persyaratan)
                                    <a href="{{ asset($k->file_persyaratan) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        Lihat File
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td>

                                @if ($k->status == 'active')
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                        Active
                                    </span>
                                @elseif ($k->status == 'completed')
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                        Completed
                                    </span>
                                @elseif ($k->status == 'approved')
                                    <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-full">
                                        Approved
                                    </span>
                                @elseif ($k->status == 'pending')
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">
                                        Pending
                                    </span>
                                @elseif ($k->status == 'rejected')
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                        Rejected
                                    </span>
                                @elseif ($k->status == 'expired')
                                    <span class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">
                                        Expired
                                    </span>
                                @elseif ($k->status == 'terminated')
                                    <span class="px-2 py-1 text-xs bg-black text-white rounded-full">
                                        Terminated
                                    </span>
                                @elseif ($k->status == 'dibuat')
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                        Dibuat
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                        {{ $k->status }}
                                    </span>
                                @endif

                            </td>

                            {{-- ACTION --}}
                            <td class="p-3 text-center space-x-2">

                                <button onclick='openEditModal(@json($k))'
                                    class="text-blue-600 hover:underline text-sm">
                                    Edit
                                </button>

                                <button onclick="openDetailModal({{ $k }})"
                                    class="text-green-600 hover:underline text-sm">
                                    Detail
                                </button>

                                <form action="{{ route('kontrak.destroy', $k->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Hapus kontrak ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600 hover:underline text-sm">
                                        Hapus
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">
                                Data kontrak belum ada
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

            {{-- ================= MODAL CREATE KONTRAK ================= --}}
            <div id="modalCreate" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

                <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-5">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">Tambah Kontrak</h2>

                        <button onclick="closeModal('modalCreate')" class="text-red-500 text-xl">&times;</button>
                    </div>

                    {{-- FORM --}}
                    <form action="{{ route('kontrak.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-3">
                        @csrf

                        {{-- PENAWARAN --}}
                        <div>
                            <label class="text-sm font-semibold">Penawaran</label>
                            <select name="penawaran_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                <option value="">-- Pilih Penawaran --</option>
                                @foreach ($penawarans as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->no_penawaran ?? 'Penawaran #' . $p->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>




                        {{-- TANGGAL KONTRAK --}}
                        <div>
                            <label class="text-sm font-semibold">Tanggal Kontrak</label>
                            <input type="date" name="tanggal_kontrak" class="w-full border rounded-lg px-3 py-2 text-sm"
                                required>
                        </div>

                        {{-- PERJANJIAN PEMBAYARAN --}}
                        <div>
                            <label class="text-sm font-semibold">Perjanjian Pembayaran</label>
                            <input type="date" name="perjanjian_pembayaran"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            {{-- PIHAK 1 --}}
                            <div>
                                <label class="text-sm font-semibold">Pihak Pertama</label>
                                <input type="text" name="pihak_pertama"
                                    class="w-full border rounded-lg px-3 py-2 text-sm" required>
                            </div>

                            {{-- CONTACT 1 --}}
                            <div>
                                <label class="text-sm font-semibold">Kontak Pihak 1</label>
                                <input type="text" name="contact_pertama"
                                    class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>

                            {{-- PIHAK 2 --}}
                            <div>
                                <label class="text-sm font-semibold">Pihak Kedua</label>
                                <input type="text" name="pihak_kedua"
                                    class="w-full border rounded-lg px-3 py-2 text-sm" required>
                            </div>

                            {{-- CONTACT 2 --}}
                            <div>
                                <label class="text-sm font-semibold">Kontak Pihak 2</label>
                                <input type="text" name="contact_kedua"
                                    class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>

                        </div>

                        {{-- FILE KONTRAK --}}
                        <div>
                            <label class="text-sm font-semibold">File Kontrak</label>
                            <input type="file" name="file_kontrak" class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        {{-- FILE PERSYARATAN --}}
                        <div>
                            <label class="text-sm font-semibold">File Persyaratan</label>
                            <input type="file" name="file_persyaratan"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <label class="text-sm font-semibold">Status</label>

                            <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm" required>

                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="active">Active</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
                                <option value="completed">Completed</option>

                            </select>
                        </div>

                        {{-- BUTTON --}}
                        <div class="flex justify-end gap-2 pt-3">
                            <button type="button" onclick="closeModal('modalCreate')"
                                class="px-4 py-2 text-sm bg-gray-200 rounded-lg">
                                Batal
                            </button>

                            <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">
                                Simpan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- ================= MODAL EDIT KONTRAK ================= --}}
            <div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-auto">

                <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-5">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">Edit Kontrak</h2>

                        <button onclick="closeModal('modalEdit')" class="text-red-500 text-xl">&times;</button>
                    </div>

                    {{-- FORM --}}
                    <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        @method('PUT')

                        {{-- PENAWARAN --}}
                        <div>
                            <label class="text-sm font-semibold">Penawaran</label>
                            <select name="penawaran_id" id="edit_penawaran_id"
                                class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                @foreach ($penawarans as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->no_penawaran ?? 'Penawaran #' . $p->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NO KONTRAK --}}
                        <div>
                            <label class="text-sm font-semibold">No Kontrak</label>
                            <input type="text" name="no_kontrak" id="edit_no_kontrak"
                                class="w-full border rounded-lg px-3 py-2 text-sm" required>
                        </div>

                        {{-- TANGGAL --}}
                        <div>
                            <label class="text-sm font-semibold">Tanggal Kontrak</label>
                            <input type="date" name="tanggal_kontrak" id="edit_tanggal_kontrak"
                                class="w-full border rounded-lg px-3 py-2 text-sm" required>
                        </div>

                        {{-- PERJANJIAN --}}
                        <div>
                            <label class="text-sm font-semibold">Perjanjian Pembayaran</label>
                            <input type="date" name="perjanjian_pembayaran" id="edit_perjanjian_pembayaran"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label class="text-sm font-semibold">Pihak Pertama</label>
                                <input type="text" name="pihak_pertama" id="edit_pihak_pertama"
                                    class="w-full border rounded-lg px-3 py-2 text-sm" required>
                            </div>

                            <div>
                                <label class="text-sm font-semibold">Kontak Pihak 1</label>
                                <input type="text" name="contact_pertama" id="edit_contact_pertama"
                                    class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="text-sm font-semibold">Pihak Kedua</label>
                                <input type="text" name="pihak_kedua" id="edit_pihak_kedua"
                                    class="w-full border rounded-lg px-3 py-2 text-sm" required>
                            </div>

                            <div>
                                <label class="text-sm font-semibold">Kontak Pihak 2</label>
                                <input type="text" name="contact_kedua" id="edit_contact_kedua"
                                    class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>

                        </div>

                        {{-- STATUS --}}
                        <div>
                            <label class="text-sm font-semibold">Status</label>

                            <select name="status" id="edit_status" class="w-full border rounded-lg px-3 py-2 text-sm"
                                required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="active">Active</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
                                <option value="completed">Completed</option>

                            </select>
                        </div>

                        {{-- FILE --}}
                        <div class="text-xs text-gray-500">
                            File baru akan menggantikan file lama (optional)
                        </div>

                        <div>
                            <label class="text-sm font-semibold">File Kontrak</label>
                            <input type="file" name="file_kontrak" class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="text-sm font-semibold">File Persyaratan</label>
                            <input type="file" name="file_persyaratan"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        {{-- BUTTON --}}
                        <div class="flex justify-end gap-2 pt-3">

                            <button type="button" onclick="closeModal('modalEdit')"
                                class="px-4 py-2 text-sm bg-gray-200 rounded-lg">
                                Batal
                            </button>

                            <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">
                                Update
                            </button>

                        </div>

                    </form>
                </div>
            </div>

            {{-- ================= MODAL DETAIL KONTRAK ================= --}}
            <div id="modalDetail" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

                <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-5">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">Detail Kontrak</h2>

                        <button onclick="closeModal('modalDetail')" class="text-red-500 text-xl">&times;</button>
                    </div>

                    <div class="space-y-2 text-sm">

                        <p><strong>No Kontrak:</strong> <span id="d_no_kontrak"></span></p>
                        <p><strong>Penawaran:</strong> <span id="d_penawaran"></span></p>
                        <p><strong>Tanggal Kontrak:</strong> <span id="d_tanggal"></span></p>
                        <p><strong>Pihak 1:</strong> <span id="d_pihak1"></span></p>
                        <p><strong>Kontak 1:</strong> <span id="d_contact1"></span></p>
                        <p><strong>Pihak 2:</strong> <span id="d_pihak2"></span></p>
                        <p><strong>Kontak 2:</strong> <span id="d_contact2"></span></p>
                        <p><strong>Status:</strong> <span id="d_status"></span></p>

                        <div class="mt-3">
                            <p class="font-semibold">File Kontrak:</p>
                            <a id="d_file_kontrak" href="#" target="_blank"
                                class="text-blue-600 hover:underline">Lihat File</a>
                        </div>

                        <div class="mt-2">
                            <p class="font-semibold">File Persyaratan:</p>
                            <a id="d_file_persyaratan" href="#" target="_blank"
                                class="text-blue-600 hover:underline">Lihat File</a>
                        </div>

                    </div>

                    <div class="mt-5 text-right">
                        <button onclick="closeModal('modalDetail')" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function openEditModal(data) {

            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('modalEdit').classList.add('flex');

            // set action form
            document.getElementById('editForm').action = `/admin/kontrak/${data.id}`;

            // fill data
            document.getElementById('edit_penawaran_id').value = data.penawaran_id ?? '';
            document.getElementById('edit_no_kontrak').value = data.no_kontrak ?? '';
            document.getElementById('edit_tanggal_kontrak').value =
                data.tanggal_kontrak ? data.tanggal_kontrak.substring(0, 10) : '';

            document.getElementById('edit_perjanjian_pembayaran').value =
                data.perjanjian_pembayaran ? data.perjanjian_pembayaran.substring(0, 10) : '';

            document.getElementById('edit_pihak_pertama').value = data.pihak_pertama ?? '';
            document.getElementById('edit_contact_pertama').value = data.contact_pertama ?? '';

            document.getElementById('edit_pihak_kedua').value = data.pihak_kedua ?? '';
            document.getElementById('edit_contact_kedua').value = data.contact_kedua ?? '';

            let status = data.status;

            if (!['dibuat', 'pending', 'approved', 'active', 'rejected', 'expired', 'completed', 'terminated'].includes(
                    status)) {
                status = 'pending';
            }

            document.getElementById('edit_status').value = status;
        }

        function openDetailModal(data) {

            document.getElementById('modalDetail').classList.remove('hidden');
            document.getElementById('modalDetail').classList.add('flex');

            document.getElementById('d_no_kontrak').innerText = data.no_kontrak ?? '-';
            document.getElementById('d_penawaran').innerText = data.penawaran?.no_penawaran ?? '-';
            document.getElementById('d_tanggal').innerText = data.tanggal_kontrak ?? '-';
            document.getElementById('d_pihak1').innerText = data.pihak_pertama ?? '-';
            document.getElementById('d_contact1').innerText = data.contact_pertama ?? '-';
            document.getElementById('d_pihak2').innerText = data.pihak_kedua ?? '-';
            document.getElementById('d_contact2').innerText = data.contact_kedua ?? '-';
            document.getElementById('d_status').innerText = data.status ?? '-';

            // file kontrak
            if (data.file_kontrak) {
                document.getElementById('d_file_kontrak').href = '/' + data.file_kontrak;
            } else {
                document.getElementById('d_file_kontrak').innerText = 'Tidak ada file';
                document.getElementById('d_file_kontrak').removeAttribute('href');
            }

            // file persyaratan
            if (data.file_persyaratan) {
                document.getElementById('d_file_persyaratan').href = '/' + data.file_persyaratan;
            } else {
                document.getElementById('d_file_persyaratan').innerText = 'Tidak ada file';
                document.getElementById('d_file_persyaratan').removeAttribute('href');
            }
        }
    </script>
@endsection
