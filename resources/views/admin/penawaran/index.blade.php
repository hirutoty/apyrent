@extends('admin.layouts.app')

@section('title', 'Data Penawaran Kendaraan')

@section('content')
    <div class="p-5">

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b p-5">

                <div>
                    <h2 class="text-xl font-bold text-gray-800">Data Penawaran Kendaraan</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Kelola seluruh penawaran kendaraan.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    <a href="{{ route('penawaran.pdf') }}" target="_blank"
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="fa fa-file-pdf mr-2"></i>
                        Export PDF
                    </a>

                    <button type="button" id="btnTambah"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fa fa-plus mr-2"></i>
                        Tambah Penawaran
                    </button>

                </div>

            </div>

            {{-- NAV TABS --}}
            <div class="border-b border-gray-200">
                <nav class="flex gap-0 -mb-px overflow-x-auto">
                    @php
                        $navItems = [
                            ['label' => 'Summary', 'url' => '/admin/summary', 'icon' => 'bi bi-bar-chart-line'],
                            [
                                'label' => 'Penawaran',
                                'url' => '/admin/penawaran',
                                'icon' => 'bi bi-file-earmark-richtext',
                            ],
                            ['label' => 'Kontrak', 'url' => '/admin/kontrak', 'icon' => 'bi bi-file-earmark-lock'],
                            ['label' => 'Invoice', 'url' => '/admin/invoices', 'icon' => 'bi bi-receipt-cutoff'],
                            ['label' => 'Payments', 'url' => '/admin/payments', 'icon' => 'bi bi-credit-card-2-front'],
                            ['label' => 'Reminders', 'url' => '/admin/reminders', 'icon' => 'bi bi-bell'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        @php
                            $isActive =
                                request()->is(ltrim($item['url'], '/')) ||
                                request()->is(ltrim($item['url'], '/') . '/*');
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
            <div class="p-5 border-b">
                <form method="GET">
                    <div class="flex gap-3 items-center">
                        <div class="relative flex-1">
                            <i class="fa fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nomor penawaran atau customer..."
                                class="w-full border rounded-lg pl-10 pr-4 py-2">
                        </div>
                        <button class="bg-gray-800 text-white px-5 rounded-lg py-2">Cari</button>

                        {{-- Tombol + Dropdown Toggle Kolom --}}
                        <div id="colToggleWrap" class="relative">
                            <button type="button" onclick="toggleColDropdown()"
                                class="inline-flex items-center gap-2 px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa fa-columns"></i> Kolom
                                <i class="fa fa-chevron-down text-xs"></i>
                            </button>

                            <div id="colDropdown"
                                class="hidden absolute right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-3 min-w-[160px] max-h-64 overflow-y-auto">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase mb-2">Tampilkan Kolom</p>

                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-nopenawaran" checked onchange="toggleColumn('col-nopenawaran', this.checked)"> No Penawaran
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-tanggal" checked onchange="toggleColumn('col-tanggal', this.checked)"> Tanggal
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-periode" checked onchange="toggleColumn('col-periode', this.checked)"> Periode
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-customer" checked onchange="toggleColumn('col-customer', this.checked)"> Customer
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-jenis" checked onchange="toggleColumn('col-jenis', this.checked)"> Jenis Customer
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-kendaraan" checked onchange="toggleColumn('col-kendaraan', this.checked)"> Kendaraan
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-total" checked onchange="toggleColumn('col-total', this.checked)"> Total
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-status" checked onchange="toggleColumn('col-status', this.checked)"> Status
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-penawaran" checked onchange="toggleColumn('col-penawaran', this.checked)"> Penawaran
                                </label>
                                <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-gray-800">
                                    <input type="checkbox" class="col-toggle" data-col="col-aksi" checked onchange="toggleColumn('col-aksi', this.checked)"> Aksi
                                </label>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left" data-col="col-nopenawaran">No Penawaran</th>
                            <th class="px-4 py-3 text-left" data-col="col-tanggal">Tanggal</th>
                            <th class="px-4 py-3 text-left" data-col="col-periode">Periode</th>
                            <th class="px-4 py-3 text-left" data-col="col-customer">Customer</th>
                            <th class="px-4 py-3 text-left" data-col="col-jenis">Jenis Customer</th>
                            <th class="px-4 py-3 text-left" data-col="col-kendaraan">Kendaraan</th>
                            <th class="px-4 py-3 text-right" data-col="col-total">Total</th>
                            <th class="px-4 py-3 text-center" data-col="col-status">Status</th>
                            <th class="px-4 py-3 text-center" data-col="col-penawaran">Penawaran</th>
                            <th class="px-4 py-3 text-center" data-col="col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penawarans as $p)

                            <tr class="border-b {{ $p->isExpired ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">

                                <td class="px-4 py-3">
                                    {{ $loop->iteration + ($penawarans->firstItem() - 1) }}
                                </td>

                                <td class="px-4 py-3" data-col="col-nopenawaran">
                                    <span class="font-semibold">{{ $p->no_penawaran }}</span>
                                </td>

                                <td class="px-4 py-3" data-col="col-tanggal">
                                    {{ optional($p->tanggal_penawaran)->format('d-m-Y') }}
                                </td>

                                <td class="px-4 py-3" data-col="col-periode">
                                    {{ $p->periode }} Bulan

                                    @if (!in_array($p->status, ['approved', 'rejected', 'expired']))
                                        @if ($p->isExpired)
                                            <div class="mt-1">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-semibold">
                                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                    Expired {{ abs($p->sisaHari) }} hari
                                                </span>
                                            </div>
                                        @elseif ($p->isSoon)
                                            <div class="mt-1">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-semibold animate-pulse">
                                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>

                                                    @if ($p->sisaHari == 0)
                                                        Berakhir Hari Ini
                                                    @elseif ($p->sisaHari == 1)
                                                        Berakhir Besok
                                                    @else
                                                        Berakhir dalam {{ $p->sisaHari }} hari
                                                    @endif

                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <td class="px-4 py-3" data-col="col-customer">
                                    {{ $p->customer_name }}
                                </td>

                                <td class="px-4 py-3" data-col="col-jenis">
                                    {{ $p->jenis_member }}
                                </td>

                                <td class="px-4 py-3" data-col="col-kendaraan">
                                    @foreach ($p->items as $item)
                                        <div>
                                            • {{ optional($item->kendaraan)->merk }} -
                                            {{ optional($item->kendaraan)->nopol }}
                                        </div>
                                    @endforeach
                                </td>

                                <td class="px-4 py-3 text-right" data-col="col-total">
                                    Rp {{ number_format($p->total, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-center" data-col="col-status">
                                    @php
                                        $warna = match ($p->status) {
                                            'approved' => 'green',
                                            'active' => 'blue',
                                            'pending' => 'yellow',
                                            'rejected' => 'red',
                                            'expired' => 'gray',
                                            default => 'gray',
                                        };
                                    @endphp

                                    <span
                                        class="px-3 py-1 rounded-full text-xs bg-{{ $warna }}-100 text-{{ $warna }}-700">
                                        {{ strtoupper($p->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3" data-col="col-penawaran">
                                    @if (!in_array($p->status, ['approved', 'rejected']))
                                        @if (!in_array($p->status, ['approved', 'expired', 'rejected']))
                                            <form action="{{ route('penawaran.approve', $p->id) }}" method="POST"
                                                onsubmit="return confirm('Approve penawaran ini dan buat data rental?')">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs whitespace-nowrap">
                                                    <i class="fa fa-check mr-1"></i> Approve
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('penawaran.reject', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Batalkan penawaran ini?')">
                                            @csrf
                                            <button class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-500">
                                            Sudah memiliki status <strong>{{ ucfirst($p->status) }}</strong>.
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3" data-col="col-aksi">
                                    <div class="flex justify-center gap-2 flex-wrap">
                                        <div class="flex justify-center gap-2 flex-wrap">

                                            <button class="showBtn bg-sky-500 hover:bg-sky-600 text-white px-3 py-1 rounded"
                                                data-id="{{ $p->id }}" title="Lihat Detail">
                                                <i class="fa fa-eye"></i>
                                            </button>

                                            <button
                                                class="editBtn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                                                data-id="{{ $p->id }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <form action="{{ route('penawaran.destroy', $p->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus data?')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center py-10 text-gray-500">
                                    Belum ada data.
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="p-4 border-t">{{ $penawarans->links() }}</div>

        </div>
    </div>

    {{-- ========================= MODAL TAMBAH ========================= --}}
    <div id="modalTambah" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-[95%] max-w-7xl max-h-[95vh] overflow-y-auto">
            <form action="{{ route('penawaran.store') }}" method="POST">
                @csrf

                <div class="flex justify-between items-center border-b px-6 py-4">
                    <h2 class="text-lg font-bold">Tambah Penawaran</h2>
                    <button type="button" id="closeTambah" class="text-gray-500 hover:text-red-600">
                        <i class="fa fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium">Tanggal</label>
                            <input type="date" name="tanggal_penawaran" value="{{ date('Y-m-d') }}"
                                class="w-full border rounded-lg p-2 mt-1" required>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                        <div>
                            <label>Kepada</label>
                            <input type="text" name="kepada" class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>UP</label>
                            <input type="text" name="up" class="w-full border rounded-lg p-2 mt-1">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label>Perihal</label>
                        <input type="text" name="perihal" class="w-full border rounded-lg p-2 mt-1">
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label>Customer</label>
                            <input type="text" name="customer_name" class="w-full border rounded-lg p-2 mt-1"
                                required>
                        </div>

                        <div>
                            <label>Contact Person</label>
                            <input type="number" name="contact_person" class="w-full border rounded-lg p-2 mt-1">
                        </div>

                        <div>
                            <label>Email Person</label>
                            <input type="email" name="email_person" class="w-full border rounded-lg p-2 mt-1">
                        </div>

                        <div>
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" class="w-full border rounded-lg p-2 mt-1"></textarea>
                        </div>

                        <div>
                            <label>Jenis Member</label>
                            <select name="jenis_member" class="w-full border rounded-lg p-2 mt-1">
                                <option value="">-- Pilih Jenis Member --</option>
                                <option value="perorangan">Perorangan</option>
                                <option value="perusahaan">Perusahaan</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label>Pengirim</label>
                            <input type="text" name="pengirim" class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Staff</label>
                            <select name="staff" class="w-full border rounded-lg p-2 mt-1">
                                <option value="">-- Pilih Jabatan Staf --</option>
                                <option>Direktur Utama (CEO)</option>
                                <option>Wakil Direktur (Vice President)</option>
                                <option>Manajer Umum (General Manager)</option>
                                <option>Manajer Operasional</option>
                                <option>Manajer Keuangan</option>
                                <option>Manajer Pemasaran</option>
                                <option>Manajer SDM (HR Manager)</option>
                                <option>Supervisor / Koordinator</option>
                                <option>Staf Administrasi</option>
                                <option>Staf Keuangan</option>
                                <option>Staf Pemasaran</option>
                                <option>Staf IT</option>
                                <option>Customer Service</option>
                                <option>Office Boy / Office Girl</option>
                                <option>Security</option>
                            </select>
                        </div>
                        <div>
                            <label>Nama Staff</label>
                            <input type="text" name="name_staff" class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Direktur</label>
                            <select name="direktur" class="w-full border rounded-lg p-2 mt-1">
                                <option value="">-- Pilih Jabatan Direktur --</option>
                                <option>Direktur Utama (CEO)</option>
                                <option>Wakil Direktur (Vice President)</option>
                                <option>Manajer Umum (General Manager)</option>
                                <option>Manajer Operasional</option>
                                <option>Manajer Keuangan</option>
                                <option>Manajer Pemasaran</option>
                                <option>Manajer SDM (HR Manager)</option>
                                <option>Supervisor / Koordinator</option>
                                <option>Staf Administrasi</option>
                                <option>Staf Keuangan</option>
                                <option>Staf Pemasaran</option>
                                <option>Staf IT</option>
                                <option>Customer Service</option>
                                <option>Office Boy / Office Girl</option>
                                <option>Security</option>
                            </select>
                        </div>
                        <div>
                            <label>Nama Direktur</label>
                            <input type="text" name="name_direktur" class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Periode</label>

                            <div class="flex mt-1">
                                <input type="number" name="periode" placeholder="12"
                                    class="w-full border border-r-0 rounded-l-lg p-2">

                                <span
                                    class="px-4 flex items-center border border-l-0 rounded-r-lg bg-gray-100 text-gray-600">
                                    Bulan
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-bold">Kendaraan</h4>
                        <button type="button" id="btnTambahItem" class="bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fa fa-plus"></i> Tambah Kendaraan
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Kendaraan</th>
                                    <th class="border p-2">Qty</th>
                                    <th class="border p-2">Tahun</th>
                                    <th class="border p-2">Harga</th>
                                    <th class="border p-2">Durasi</th>
                                    <th class="border p-2">Satuan</th>
                                    <th class="border p-2"></th>
                                </tr>
                            </thead>
                            <tbody id="itemContainer"></tbody>
                        </table>
                    </div>

                    <div class="mt-5 text-right">
                        <label class="font-semibold">Total</label>
                        <input id="grandTotal" readonly class="border rounded-lg p-2 text-right font-bold w-64"
                            value="0">
                    </div>

                </div>

                <div class="border-t px-6 py-4 flex justify-end gap-2">
                    <button type="button" id="closeTambah2" class="px-5 py-2 rounded-lg border">Batal</button>
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================= MODAL EDIT ========================= --}}
    <div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-[95%] max-w-7xl max-h-[95vh] overflow-y-auto">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center border-b px-6 py-4">
                    <h2 class="text-lg font-bold">Edit Penawaran</h2>
                    <button type="button" id="closeEdit" class="text-gray-500 hover:text-red-600">
                        <i class="fa fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label>Nomor Penawaran</label>
                            <input id="edit_no_penawaran" type="text" name="no_penawaran"
                                class="w-full border rounded-lg p-2 mt-1 bg-gray-50" readonly>
                        </div>
                        <div>
                            <label>Tanggal</label>
                            <input id="edit_tanggal" type="date" name="tanggal_penawaran"
                                class="w-full border rounded-lg p-2 mt-1" required>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-5">
                        <div>
                            <label>Kepada</label>
                            <input id="edit_kepada" type="text" name="kepada"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>UP</label>
                            <input id="edit_up" type="text" name="up"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label>Perihal</label>
                        <input id="edit_perihal" type="text" name="perihal"
                            class="w-full border rounded-lg p-2 mt-1">
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label>Customer</label>
                            <input id="edit_customer" type="text" name="customer_name"
                                class="w-full border rounded-lg p-2 mt-1" required>
                        </div>

                        <div>
                            <label>Contact Person</label>
                            <input id="edit_contact" type="number" name="contact_person"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>

                        <div>
                            <label>Email Person</label>
                            <input id="edit_email" type="email" name="email_person"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>

                        <div>
                            <label>Alamat</label>
                            <textarea id="edit_alamat" name="alamat" rows="3" class="w-full border rounded-lg p-2 mt-1"></textarea>
                        </div>

                        <div>
                            <label>Jenis Member</label>
                            <select id="edit_jenis_member" name="jenis_member" class="w-full border rounded-lg p-2 mt-1">
                                <option value="">-- Pilih Jenis Member --</option>
                                <option value="Perorangan">Perorangan</option>
                                <option value="Perusahaan">Perusahaan</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label>Pengirim</label>
                            <input id="edit_pengirim" type="text" name="pengirim"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Staff</label>
                            <select id="edit_staff" name="staff" class="w-full border rounded-lg p-2 mt-1">
                                <option value="">-- Pilih Jabatan Staf --</option>
                                <option>Direktur Utama (CEO)</option>
                                <option>Wakil Direktur (Vice President)</option>
                                <option>Manajer Umum (General Manager)</option>
                                <option>Manajer Operasional</option>
                                <option>Manajer Keuangan</option>
                                <option>Manajer Pemasaran</option>
                                <option>Manajer SDM (HR Manager)</option>
                                <option>Supervisor / Koordinator</option>
                                <option>Staf Administrasi</option>
                                <option>Staf Keuangan</option>
                                <option>Staf Pemasaran</option>
                                <option>Staf IT</option>
                                <option>Customer Service</option>
                                <option>Office Boy / Office Girl</option>
                                <option>Security</option>
                            </select>
                        </div>
                        <div>
                            <label>Nama Staff</label>
                            <input id="edit_name_staff" type="text" name="name_staff"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Direktur</label>
                            <select id="edit_direktur" name="direktur" class="w-full border rounded-lg p-2 mt-1">
                                <option value="">-- Pilih Jabatan Direktur --</option>
                                <option>Direktur Utama (CEO)</option>
                                <option>Wakil Direktur (Vice President)</option>
                                <option>Manajer Umum (General Manager)</option>
                                <option>Manajer Operasional</option>
                                <option>Manajer Keuangan</option>
                                <option>Manajer Pemasaran</option>
                                <option>Manajer SDM (HR Manager)</option>
                                <option>Supervisor / Koordinator</option>
                                <option>Staf Administrasi</option>
                                <option>Staf Keuangan</option>
                                <option>Staf Pemasaran</option>
                                <option>Staf IT</option>
                                <option>Customer Service</option>
                                <option>Office Boy / Office Girl</option>
                                <option>Security</option>
                            </select>
                        </div>
                        <div>
                            <label>Nama Direktur</label>
                            <input id="edit_name_direktur" type="text" name="name_direktur"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Periode</label>

                            <div class="flex mt-1">
                                <input id="edit_periode" type="number" name="periode"
                                    class="w-full border border-r-0 rounded-l-lg p-2">

                                <span
                                    class="px-4 flex items-center border border-l-0 rounded-r-lg bg-gray-100 text-gray-600">
                                    Bulan
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-bold">Kendaraan</h4>
                        <button type="button" id="btnTambahItemEdit" class="bg-green-600 text-white px-4 py-2 rounded">
                            <i class="fa fa-plus"></i> Tambah
                        </button>
                    </div>

                    <table class="w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">Kendaraan</th>
                                <th class="border p-2">Qty</th>
                                <th class="border p-2">Tahun</th>
                                <th class="border p-2">Harga</th>
                                <th class="border p-2">Durasi</th>
                                <th class="border p-2">Satuan</th>
                                <th class="border p-2"></th>
                            </tr>
                        </thead>
                        <tbody id="editItemContainer"></tbody>
                    </table>

                    <div class="mt-5 text-right">
                        <label class="font-semibold">Total</label>
                        <input id="editGrandTotal" readonly class="border rounded-lg p-2 w-64 text-right font-bold">
                    </div>

                </div>

                <div class="border-t p-5 flex justify-end gap-2">
                    <button type="button" id="closeEdit2" class="border px-5 py-2 rounded">Batal</button>
                    <button class="bg-blue-600 text-white px-6 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================= MODAL SHOW ========================= --}}
    <div id="modalShow" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-[95%] max-w-7xl max-h-[95vh] overflow-y-auto">

            <div class="flex justify-between items-center border-b px-6 py-4">
                <h2 class="text-lg font-bold">Detail Penawaran</h2>
                <button type="button" id="closeShow" class="text-gray-500 hover:text-red-600">
                    <i class="fa fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nomor Penawaran</label>
                        <p id="show_no_penawaran" class="font-semibold text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tanggal</label>
                        <p id="show_tanggal" class="font-semibold text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <p id="show_status" class="font-semibold text-gray-800 mt-1">-</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Kepada</label>
                        <p id="show_kepada" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">UP</label>
                        <p id="show_up" class="text-gray-800 mt-1">-</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Perihal</label>
                    <p id="show_perihal" class="text-gray-800 mt-1">-</p>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Customer</label>
                        <p id="show_customer" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Contact Person</label>
                        <p id="show_contact" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p id="show_email" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Alamat</label>
                        <p id="show_alamat" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jenis Member</label>
                        <p id="show_jenis_member" class="text-gray-800 mt-1">-</p>
                    </div>
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Pengirim</label>
                        <p id="show_pengirim" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Staff</label>
                        <p id="show_staff" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nama Staff</label>
                        <p id="show_name_staff" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Direktur</label>
                        <p id="show_direktur" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nama Direktur</label>
                        <p id="show_name_direktur" class="text-gray-800 mt-1">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Periode</label>
                        <p id="show_periode" class="text-gray-800 mt-1">-</p>
                    </div>
                </div>

                <hr class="my-6">

                <h4 class="font-bold mb-3">Kendaraan</h4>

                <div class="overflow-x-auto">
                    <table class="w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">Kendaraan</th>
                                <th class="border p-2">Qty</th>
                                <th class="border p-2">Tahun</th>
                                <th class="border p-2 text-right">Harga</th>
                                <th class="border p-2">Durasi</th>
                                <th class="border p-2">Satuan</th>
                                <th class="border p-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="showItemContainer"></tbody>
                    </table>
                </div>

                <div class="mt-5 text-right">
                    <label class="font-semibold">Total</label>
                    <input id="showGrandTotal" readonly class="border rounded-lg p-2 w-64 text-right font-bold bg-gray-50"
                        value="0">
                </div>

            </div>

            <div class="border-t p-5 flex justify-end">
    <button
        type="button"
        id="closeShow2"
        class="border border-red-600 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded transition">
        Tutup
    </button>
</div>
        </div>
    </div>

    {{-- TEMPLATE ROW (untuk modal tambah) --}}
    <template id="itemTemplate">
        <tr>
            <td class="border p-2">
                <select name="kendaraan_id[]" class="kendaraan w-full border rounded p-2" required>
                    <option value="">Pilih Kendaraan</option>
                    @foreach ($kendaraans as $k)
                        <option value="{{ $k->id }}">{{ $k->merk }} - {{ $k->nopol }}</option>
                    @endforeach
                </select>
            </td>
            <td class="border p-2">
                <input type="number" name="qty[]" value="1" min="1"
                    class="qty w-full border rounded p-2">
            </td>
            <td class="border p-2">
                <input type="text" name="tahun_unit[]" class="w-full border rounded p-2">
            </td>
            <td class="border p-2">
                <input type="number" name="price[]" value="0" class="price w-full border rounded p-2">
            </td>
            <td class="border p-2">
                <input type="number" name="durasi[]" value="1" class="w-full border rounded p-2">
            </td>
            <td class="border p-2">
                <select name="satuan_durasi[]" class="w-full border rounded p-2">
                    <option value="Hari">Hari</option>
                    <option value="Bulan">Bulan</option>
                    <option value="Tahun">Tahun</option>
                </select>
            </td>
            <td class="border p-2 text-center">
                <button type="button" class="hapusItem bg-red-600 text-white px-3 py-1 rounded">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
        <script>

            function toggleColDropdown() {
        document.getElementById('colDropdown').classList.toggle('hidden');
    }

    // Tutup dropdown kalau klik di luar area
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('colToggleWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('colDropdown').classList.add('hidden');
        }
    });

    // Toggle kolom (Hide/Show TH dan TD) berdasarkan data-col attribute
    function toggleColumn(colId, show) {
        document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => {
            el.style.display = show ? '' : 'none';
        });
    }
            const kendaraanOptions = @json(
                $kendaraans->map(fn($k) => [
                        'id' => $k->id,
                        'nama' => $k->merk . ' - ' . $k->nopol,
                    ]));

            function buildKendaraanSelect(selectedId = '') {
                let opts = '<option value="">Pilih Kendaraan</option>';
                kendaraanOptions.forEach(k => {
                    opts += `<option value="${k.id}" ${k.id == selectedId ? 'selected' : ''}>${k.nama}</option>`;
                });
                return opts;
            }

            function buildSatuanSelect(selected = '') {
                return ['Hari', 'Bulan', 'Tahun'].map(s =>
                    `<option value="${s}" ${s === selected ? 'selected' : ''}>${s}</option>`
                ).join('');
            }

            function buildEditRow(item = {}) {
                return `
            <tr>
                <td class="border p-2">
                    <select name="kendaraan_id[]" class="kendaraan w-full border rounded p-2" required>
                        ${buildKendaraanSelect(item.kendaraan_id ?? '')}
                    </select>
                </td>
                <td class="border p-2">
                    <input type="number" name="qty[]" value="${item.qty ?? 1}" min="1" class="qty w-full border rounded p-2">
                </td>
                <td class="border p-2">
                    <input type="text" name="tahun_unit[]" value="${item.tahun_unit ?? ''}" class="w-full border rounded p-2">
                </td>
                <td class="border p-2">
                    <input type="number" name="price[]" value="${item.price ?? 0}" class="price w-full border rounded p-2">
                </td>
                <td class="border p-2">
                    <input type="number" name="durasi[]" value="${item.durasi ?? 1}" class="w-full border rounded p-2">
                </td>
                <td class="border p-2">
                    <select name="satuan_durasi[]" class="w-full border rounded p-2">
                        ${buildSatuanSelect(item.satuan_durasi ?? 'Hari')}
                    </select>
                </td>
                <td class="border p-2 text-center">
                    <button type="button" class="hapusEdit bg-red-600 text-white px-3 py-1 rounded">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            }

            // ---- MODAL TAMBAH ----
            const modalTambah = document.getElementById('modalTambah');
            const itemContainer = document.getElementById('itemContainer');
            const template = document.getElementById('itemTemplate');

            document.getElementById('btnTambah').onclick = () => {
                modalTambah.classList.remove('hidden');
                modalTambah.classList.add('flex');
            };

            function closeTambah() {
                modalTambah.classList.add('hidden');
                modalTambah.classList.remove('flex');
            }
            document.getElementById('closeTambah').onclick = closeTambah;
            document.getElementById('closeTambah2').onclick = closeTambah;
            modalTambah.addEventListener('click', e => {
                if (e.target === modalTambah) closeTambah();
            });

            function tambahBaris() {
                itemContainer.appendChild(template.content.cloneNode(true));
                hitungTotal();
            }
            document.getElementById('btnTambahItem').onclick = tambahBaris;

            itemContainer.addEventListener('click', function(e) {
                if (e.target.closest('.hapusItem')) {
                    e.target.closest('tr').remove();
                    hitungTotal();
                }
            });
            itemContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('price') || e.target.classList.contains('qty')) {
                    hitungTotal();
                }
            });

            function hitungTotal() {
                let total = 0;
                document.querySelectorAll('#itemContainer tr').forEach(row => {
                    total += Number(row.querySelector('.qty')?.value ?? 0) *
                        Number(row.querySelector('.price')?.value ?? 0);
                });
                document.getElementById('grandTotal').value = total.toLocaleString('id-ID');
            }

            tambahBaris();

            // ---- MODAL EDIT ----
            const modalEdit = document.getElementById('modalEdit');
            const formEdit = document.getElementById('formEdit');
            const editItemContainer = document.getElementById('editItemContainer');

            function closeEdit() {
                modalEdit.classList.add('hidden');
                modalEdit.classList.remove('flex');
            }
            document.getElementById('closeEdit').onclick = closeEdit;
            document.getElementById('closeEdit2').onclick = closeEdit;
            modalEdit.addEventListener('click', e => {
                if (e.target === modalEdit) closeEdit();
            });

            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    fetch("{{ url('admin/penawaran') }}/" + id + "/edit")
                        .then(res => res.json())
                        .then(data => {
                            modalEdit.classList.remove('hidden');
                            modalEdit.classList.add('flex');
                            formEdit.action = "{{ url('admin/penawaran') }}/" + id;

                            document.getElementById('edit_no_penawaran').value = data.no_penawaran ?? '';
                            document.getElementById('edit_tanggal').value = data.tanggal_penawaran ?? '';
                            document.getElementById('edit_kepada').value = data.kepada ?? '';
                            document.getElementById('edit_up').value = data.up ?? '';
                            document.getElementById('edit_perihal').value = data.perihal ?? '';
                            document.getElementById('edit_customer').value = data.customer_name ?? '';
                            document.getElementById('edit_contact').value = data.contact_person ?? '';
                            document.getElementById('edit_email').value = data.email_person ?? '';
                            document.getElementById('edit_alamat').value = data.alamat ?? '';
                            document.getElementById('edit_jenis_member').value = data.jenis_member ?? '';
                            document.getElementById('edit_pengirim').value = data.pengirim ?? '';
                            document.getElementById('edit_staff').value = data.staff ?? '';
                            document.getElementById('edit_name_staff').value = data.name_staff ?? '';
                            document.getElementById('edit_direktur').value = data.direktur ?? '';
                            document.getElementById('edit_name_direktur').value = data.name_direktur ?? '';
                            document.getElementById('edit_periode').value = data.periode ?? '';

                            loadEditItems(data.items ?? []);
                        })
                        .catch(err => console.error('Gagal fetch data:', err));
                });
            });

            function loadEditItems(items) {
                editItemContainer.innerHTML = '';
                const rows = items.length ? items : [{}];
                rows.forEach(item => {
                    editItemContainer.innerHTML += buildEditRow(item);
                });
                hitungEditTotal();
            }

            document.getElementById('btnTambahItemEdit').onclick = function() {
                editItemContainer.innerHTML += buildEditRow();
            };

            editItemContainer.addEventListener('click', function(e) {
                if (e.target.closest('.hapusEdit')) {
                    e.target.closest('tr').remove();
                    hitungEditTotal();
                }
            });
            editItemContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
                    hitungEditTotal();
                }
            });

            function hitungEditTotal() {
                let total = 0;
                document.querySelectorAll('#editItemContainer tr').forEach(row => {
                    total += Number(row.querySelector('.qty')?.value ?? 0) *
                        Number(row.querySelector('.price')?.value ?? 0);
                });
                document.getElementById('editGrandTotal').value = total.toLocaleString('id-ID');
            }

            // ---- MODAL SHOW ----
            const modalShow = document.getElementById('modalShow');
            const showItemContainer = document.getElementById('showItemContainer');

            function closeShow() {
                modalShow.classList.add('hidden');
                modalShow.classList.remove('flex');
            }
            document.getElementById('closeShow').onclick = closeShow;
            document.getElementById('closeShow2').onclick = closeShow;
            modalShow.addEventListener('click', e => {
                if (e.target === modalShow) closeShow();
            });

            function formatRupiah(angka) {
                return 'Rp ' + Number(angka ?? 0).toLocaleString('id-ID');
            }

            function getKendaraanName(id) {
                const found = kendaraanOptions.find(k => k.id == id);
                return found ? found.nama : '-';
            }

            function formatTanggal(tgl) {
                if (!tgl) return '-';
                const d = new Date(tgl);
                if (isNaN(d)) return tgl;
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            document.querySelectorAll('.showBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    fetch("{{ url('admin/penawaran') }}/" + id + "/edit")
                        .then(res => res.json())
                        .then(data => {
                            modalShow.classList.remove('hidden');
                            modalShow.classList.add('flex');

                            document.getElementById('show_no_penawaran').textContent = data.no_penawaran ??
                                '-';
                            document.getElementById('show_tanggal').textContent = formatTanggal(data
                                .tanggal_penawaran);
                            document.getElementById('show_status').textContent = (data.status ?? '-')
                                .toString().toUpperCase();
                            document.getElementById('show_kepada').textContent = data.kepada ?? '-';
                            document.getElementById('show_up').textContent = data.up ?? '-';
                            document.getElementById('show_perihal').textContent = data.perihal ?? '-';
                            document.getElementById('show_customer').textContent = data.customer_name ??
                            '-';
                            document.getElementById('show_contact').textContent = data.contact_person ??
                            '-';
                            document.getElementById('show_email').textContent = data.email_person ?? '-';
                            document.getElementById('show_alamat').textContent = data.alamat ?? '-';
                            document.getElementById('show_jenis_member').textContent = data.jenis_member ??
                                '-';
                            document.getElementById('show_pengirim').textContent = data.pengirim ?? '-';
                            document.getElementById('show_staff').textContent = data.staff ?? '-';
                            document.getElementById('show_name_staff').textContent = data.name_staff ?? '-';
                            document.getElementById('show_direktur').textContent = data.direktur ?? '-';
                            document.getElementById('show_name_direktur').textContent = data
                                .name_direktur ?? '-';
                            document.getElementById('show_periode').textContent = (data.periode ?? '-') +
                                ' Bulan';

                            loadShowItems(data.items ?? []);
                        })
                        .catch(err => console.error('Gagal fetch data:', err));
                });
            });

            function loadShowItems(items) {
                showItemContainer.innerHTML = '';
                let total = 0;

                if (!items.length) {
                    showItemContainer.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-gray-500 border">Tidak ada kendaraan.</td>
            </tr>`;
                }

                items.forEach(item => {
                    const subtotal = Number(item.qty ?? 0) * Number(item.price ?? 0);
                    total += subtotal;

                    showItemContainer.innerHTML += `
            <tr>
                <td class="border p-2">${getKendaraanName(item.kendaraan_id)}</td>
                <td class="border p-2 text-center">${item.qty ?? 0}</td>
                <td class="border p-2 text-center">${item.tahun_unit ?? '-'}</td>
                <td class="border p-2 text-right">${formatRupiah(item.price)}</td>
                <td class="border p-2 text-center">${item.durasi ?? '-'}</td>
                <td class="border p-2 text-center">${item.satuan_durasi ?? '-'}</td>
                <td class="border p-2 text-right">${formatRupiah(subtotal)}</td>
            </tr>`;
                });

                document.getElementById('showGrandTotal').value = formatRupiah(total);
            }
        </script>
    @endpush

@endsection
