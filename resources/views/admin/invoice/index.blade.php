@extends('admin.layouts.app')

@section('title', 'Data Invoice')

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
                    <h2 class="text-xl font-bold text-gray-800">Data Invoice</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola seluruh data invoice.</p>
                </div>
                <button type="button" id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fa fa-plus mr-2"></i> Tambah Invoice
                </button>
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
                            {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- SEARCH --}}
            <div class="p-5 border-b">
                <form method="GET">
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <i class="fa fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari no invoice, customer, atau order..."
                                class="w-full border rounded-lg pl-10 pr-4 py-2">
                        </div>
                        <button class="bg-gray-800 text-white px-5 rounded-lg">Cari</button>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">No Invoice</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-left">Penawaran</th>
                            <th class="px-4 py-3 text-left">Kontrak</th>
                            <th class="px-4 py-3 text-left">Kendaraan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Pembayaran</th>
                            <th class="px-4 py-3 text-center">Terakhir Reminder</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $loop->iteration + ($invoices->firstItem() - 1) }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold">{{ $inv->invoice_no }}</span>
                                </td>
                                <td class="px-4 py-3">{{ optional($inv->invoice_date)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3">{{ $inv->customer_name }}</td>
                                <td class="px-4 py-3">{{ optional($inv->penawaran)->no_penawaran ?? '-' }}</td>
                                <td class="px-4 py-3">{{ optional($inv->kontrak)->no_kontrak ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($inv->kendaraan)
                                        {{ $inv->kendaraan->merk }} - {{ $inv->kendaraan->nopol }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @php
                                        $warna = match ($inv->status) {
                                            'lunas' => 'green',
                                            'partial' => 'yellow',
                                            'overdue' => 'red',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs bg-{{ $warna }}-100 text-{{ $warna }}-700">
                                        {{ $inv->status ? strtoupper($inv->status) : '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @php
                                        $warnaBayar = match ($inv->payment_status) {
                                            'paid' => 'green',
                                            'unpaid' => 'red',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs bg-{{ $warnaBayar }}-100 text-{{ $warnaBayar }}-700">
                                        {{ $inv->payment_status ? strtoupper($inv->payment_status) : '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $inv->last_email_sent_at?->format('d-F-Y') }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2 flex-wrap">



                                        {{-- Kirim Email --}}
                                        <form action="{{ route('invoices.email', $inv->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-600 text-white px-3 py-1 rounded">
                                                <i class="fa fa-envelope"></i> Kirim Email
                                            </button>
                                        </form>

                                        {{-- Download PDF --}}
                                        <a href="{{ route('invoices.print', $inv->id) }}"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"
                                            target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>

                                        {{-- Tombol Show --}}
                                        <button type="button"
                                            class="showBtn bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded"
                                            data-id="{{ $inv->id }}" data-invoice_no="{{ $inv->invoice_no }}"
                                            data-invoice_date="{{ optional($inv->invoice_date)->format('d-m-Y') }}"
                                            data-customer_name="{{ $inv->customer_name }}" data-type="{{ $inv->type }}"
                                            data-customer_address="{{ $inv->customer_address }}"
                                            data-telephone="{{ $inv->telephone }}" data-email="{{ $inv->email }}"
                                            data-contact_person="{{ $inv->contact_person }}"
                                            data-penawaran="{{ optional($inv->penawaran)->no_penawaran ?? '-' }}"
                                            data-kontrak="{{ optional($inv->kontrak)->no_kontrak ?? '-' }}"
                                            data-kendaraan="{{ $inv->kendaraan ? $inv->kendaraan->merk . ' - ' . $inv->kendaraan->nopol : '-' }}"
                                            data-satuan="{{ $inv->satuan }}" data-pengirim="{{ $inv->pengirim }}"
                                            data-ppn="{{ $inv->ppn }}" data-pph="{{ $inv->pph }}"
                                            data-total="{{ $inv->total }}" data-status="{{ $inv->status }}"
                                            data-payment_status="{{ $inv->payment_status }}"
                                            data-staff="{{ $inv->staff }}" data-name_staff="{{ $inv->name_staff }}"
                                            data-direktur="{{ $inv->direktur }}"
                                            data-name_direktur="{{ $inv->name_direktur }}"
                                            data-ttd_staff="{{ $inv->ttd_staff }}"
                                            data-ttd_direktur="{{ $inv->ttd_direktur }}">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        {{-- Tombol Edit — semua data disimpan di data-* --}}
                                        <button type="button"
                                            class="editBtn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                                            data-id="{{ $inv->id }}" data-invoice_no="{{ $inv->invoice_no }}"
                                            data-invoice_date="{{ optional($inv->invoice_date)->format('Y-m-d') }}"
                                            data-customer_name="{{ $inv->customer_name }}" data-type="{{ $inv->type }}"
                                            data-customer_address="{{ $inv->customer_address }}"
                                            data-telephone="{{ $inv->telephone }}" data-email="{{ $inv->email }}"
                                            data-contact_person="{{ $inv->contact_person }}"
                                            data-penawaran_id="{{ $inv->penawaran_id }}"
                                            data-kontrak_id="{{ $inv->kontrak_id }}"
                                            data-kendaraan_id="{{ $inv->kendaraan_id }}"
                                            data-satuan="{{ $inv->satuan }}" data-pengirim="{{ $inv->pengirim }}"
                                            data-ppn="{{ $inv->ppn }}" data-pph="{{ $inv->pph }}"
                                            data-total="{{ $inv->total }}" data-status="{{ $inv->status }}"
                                            data-payment_status="{{ $inv->payment_status }}"
                                            data-staff="{{ $inv->staff }}" data-name_staff="{{ $inv->name_staff }}"
                                            data-direktur="{{ $inv->direktur }}"
                                            data-name_direktur="{{ $inv->name_direktur }}"
                                            data-ttd_staff="{{ $inv->ttd_staff }}"
                                            data-ttd_direktur="{{ $inv->ttd_direktur }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data invoice ini?')">
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
                                <td colspan="10" class="text-center py-10 text-gray-500">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="p-4 border-t">{{ $invoices->links() }}</div>

        </div>
    </div>

    {{-- ========================= MODAL TAMBAH ========================= --}}
    <div id="modalTambah" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-3xl max-h-[95vh] overflow-y-auto">
            <form action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Tambah Invoice</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Isi data invoice dengan lengkap</p>
                    </div>
                    <button type="button" id="closeTambah"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-6">

                    {{-- SEKSI 1 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">1</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No invoice</label>
                                <input type="text" value="(otomatis dibuat sistem)" disabled
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal invoice <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama customer <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" required
                                    placeholder="Nama customer atau perusahaan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe customer</label>
                                <select name="type"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="perorangan">Perorangan</option>
                                    <option value="perusahaan">Perusahaan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat customer</label>
                                <textarea name="customer_address" rows="2" placeholder="Alamat lengkap customer"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Telepon</label>
                                <input type="text" name="telephone" placeholder="Nomor telepon customer"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 mt-3">Email</label>
                                <input type="email" name="email" placeholder="Email Customer"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 2 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">2</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relasi dokumen</h3>
                            <span class="text-xs text-gray-400">(opsional)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                                <select name="penawaran_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($penawarans as $p)
                                        <option value="{{ $p->id }}">{{ $p->no_penawaran }} –
                                            {{ $p->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontrak</label>
                                <select name="kontrak_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($kontraks as $k)
                                        <option value="{{ $k->id }}">{{ $k->no_kontrak ?? '#' . $k->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                                <select name="kendaraan_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($kendaraans as $kd)
                                        <option value="{{ $kd->id }}">{{ $kd->merk }} – {{ $kd->nopol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 3 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">3</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi invoice</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan</label>
                                <input type="text" name="satuan" placeholder="Contoh: Unit, Pcs"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pengirim</label>
                                <input type="text" name="pengirim" placeholder="Nama pengirim"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPN (%)</label>
                                <input type="number" step="0.01" name="ppn" value="0" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPH (%)</label>
                                <input type="number" step="0.01" name="pph" value="0" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total (Rp)</label>
                                <input type="number" step="0.01" name="total" value="0" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 4 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">4</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status invoice</label>
                                <select name="status"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="draft">Draft</option>
                                    <option value="partial">Partial</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status pembayaran</label>
                                <select name="payment_status"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 5 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">5</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penandatangan</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i
                                        class="fa fa-user text-gray-400 mr-1"></i> Staff</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan staff</label>
                                    <input type="text" name="staff" placeholder="Contoh: Manajer Keuangan"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama staff</label>
                                    <input type="text" name="name_staff" placeholder="Nama lengkap"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan
                                        Staff</label>
                                    <input type="file" name="ttd_staff" accept="image/*"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i
                                        class="fa fa-user-tie text-gray-400 mr-1"></i> Direktur</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan
                                        direktur</label>
                                    <input type="text" name="direktur" placeholder="Contoh: Direktur Utama"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama direktur</label>
                                    <input type="text" name="name_direktur" placeholder="Nama lengkap"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan
                                        Direktur</label>
                                    <input type="file" name="ttd_direktur" accept="image/*"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button" id="closeTambah2"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                        <i class="fa fa-save text-sm"></i> Simpan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================= MODAL SHOW ========================= --}}
    <div id="modalShow" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-3xl max-h-[95vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Detail Invoice</h2>
                    <p id="show_subtitle" class="text-xs text-gray-400 mt-0.5"></p>
                </div>
                <button type="button" id="closeShow"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6">

                {{-- SEKSI 1: INFO DASAR --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">1</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">No Invoice</p>
                            <p id="show_invoice_no" class="text-sm font-semibold text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tanggal Invoice</p>
                            <p id="show_invoice_date" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Nama Customer</p>
                            <p id="show_customer_name" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tipe Customer</p>
                            <p id="show_type" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Alamat Customer</p>
                            <p id="show_customer_address" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Telepon</p>
                            <p id="show_telephone" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Email</p>
                            <p id="show_email" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Contact Person</p>
                            <p id="show_contact_person" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 2: RELASI --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">2</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relasi dokumen</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Penawaran</p>
                            <p id="show_penawaran" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kontrak</p>
                            <p id="show_kontrak" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kendaraan</p>
                            <p id="show_kendaraan" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 3: INFO INVOICE --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">3</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi invoice</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Satuan</p>
                            <p id="show_satuan" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Pengirim</p>
                            <p id="show_pengirim" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">PPN (%)</p>
                            <p id="show_ppn" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">PPH (%)</p>
                            <p id="show_pph" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-blue-50 rounded-xl px-5 py-4 flex items-center justify-between">
                        <span class="text-sm font-semibold text-blue-700">Total</span>
                        <span id="show_total" class="text-xl font-bold text-blue-700">Rp 0</span>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 4: STATUS --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">4</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h3>
                    </div>
                    <div class="flex gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1.5">Status Invoice</p>
                            <span id="show_status" class="px-3 py-1 rounded-full text-xs font-semibold">-</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1.5">Status Pembayaran</p>
                            <span id="show_payment_status" class="px-3 py-1 rounded-full text-xs font-semibold">-</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 5: PENANDATANGAN --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">5</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penandatangan</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-500"><i class="fa fa-user text-gray-400 mr-1"></i>
                                Staff</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Jabatan</p>
                                    <p id="show_staff" class="text-sm text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                                    <p id="show_name_staff" class="text-sm text-gray-800">-</p>
                                </div>
                            </div>
                            <div id="show_ttd_staff_wrap" class="hidden">
                                <p class="text-xs text-gray-400 mb-1">Tanda Tangan</p>
                                <img id="show_ttd_staff_img" src="" alt="TTD Staff" class="w-28 rounded border">
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-500"><i
                                    class="fa fa-user-tie text-gray-400 mr-1"></i> Direktur</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Jabatan</p>
                                    <p id="show_direktur" class="text-sm text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                                    <p id="show_name_direktur" class="text-sm text-gray-800">-</p>
                                </div>
                            </div>
                            <div id="show_ttd_direktur_wrap" class="hidden">
                                <p class="text-xs text-gray-400 mb-1">Tanda Tangan</p>
                                <img id="show_ttd_direktur_img" src="" alt="TTD Direktur"
                                    class="w-28 rounded border">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex justify-end">
                <button type="button" id="closeShow2"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ========================= MODAL EDIT ========================= --}}
    <div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-3xl max-h-[95vh] overflow-y-auto">
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Edit Invoice</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Perbarui data invoice</p>
                    </div>
                    <button type="button" id="closeEdit"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-6">

                    {{-- SEKSI 1 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">1</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No invoice</label>
                                <input id="edit_invoice_no" type="text" readonly
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal invoice <span
                                        class="text-red-500">*</span></label>
                                <input id="edit_invoice_date" type="date" name="invoice_date" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama customer <span
                                        class="text-red-500">*</span></label>
                                <input id="edit_customer_name" type="text" name="customer_name" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe customer</label>
                                <select id="edit_type" name="type"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="perorangan">Perorangan</option>
                                    <option value="perusahaan">Perusahaan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat customer</label>
                                <textarea id="edit_customer_address" name="customer_address" rows="2"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Telepon</label>
                                <input id="edit_telephone" type="text" name="telephone"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                                <input id="edit_email" type="email" name="email"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact person</label>
                                <input id="edit_contact_person" type="text" name="contact_person"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 2 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">2</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relasi dokumen</h3>
                            <span class="text-xs text-gray-400">(opsional)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                                <select id="edit_penawaran_id" name="penawaran_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($penawarans as $p)
                                        <option value="{{ $p->id }}">{{ $p->no_penawaran }} –
                                            {{ $p->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontrak</label>
                                <select id="edit_kontrak_id" name="kontrak_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($kontraks as $k)
                                        <option value="{{ $k->id }}">{{ $k->no_kontrak ?? '#' . $k->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                                <select id="edit_kendaraan_id" name="kendaraan_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($kendaraans as $kd)
                                        <option value="{{ $kd->id }}">{{ $kd->merk }} – {{ $kd->nopol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 3 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">3</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi invoice</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan</label>
                                <input id="edit_satuan" type="text" name="satuan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pengirim</label>
                                <input id="edit_pengirim" type="text" name="pengirim"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPN (%)</label>
                                <input id="edit_ppn" type="number" step="0.01" name="ppn" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPH (%)</label>
                                <input id="edit_pph" type="number" step="0.01" name="pph" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total (Rp)</label>
                                <input id="edit_total" type="number" step="0.01" name="total" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 4 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">4</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status invoice</label>
                                <select id="edit_status" name="status"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="draft">Draft</option>
                                    <option value="partial">Partial</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status pembayaran</label>
                                <select id="edit_payment_status" name="payment_status"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 5 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">5</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penandatangan</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i
                                        class="fa fa-user text-gray-400 mr-1"></i> Staff</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan staff</label>
                                    <input id="edit_staff" type="text" name="staff"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama staff</label>
                                    <input id="edit_name_staff" type="text" name="name_staff"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan
                                        Staff</label>
                                    <input id="edit_ttd_staff" type="file" name="ttd_staff" accept="image/*"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                                    <div id="preview_ttd_staff" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i
                                        class="fa fa-user-tie text-gray-400 mr-1"></i> Direktur</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan
                                        direktur</label>
                                    <input id="edit_direktur" type="text" name="direktur"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama direktur</label>
                                    <input id="edit_name_direktur" type="text" name="name_direktur"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan
                                        Direktur</label>
                                    <input id="edit_ttd_direktur" type="file" name="ttd_direktur" accept="image/*"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                                    <div id="preview_ttd_direktur" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button" id="closeEdit2"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                        <i class="fa fa-save text-sm"></i> Update Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // ===================== HELPER =====================
            function openModal(el) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            }

            function closeModal(el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }

            function setVal(id, value) {
                const el = document.getElementById(id);
                if (el) el.value = value ?? '';
            }

            function setText(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '-';
            }

            function formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }

            // ===================== MODAL TAMBAH =====================
            const modalTambah = document.getElementById('modalTambah');
            document.getElementById('btnTambah').onclick = () => openModal(modalTambah);
            document.getElementById('closeTambah').onclick = () => closeModal(modalTambah);
            document.getElementById('closeTambah2').onclick = () => closeModal(modalTambah);
            modalTambah.addEventListener('click', e => {
                if (e.target === modalTambah) closeModal(modalTambah);
            });

            // ===================== MODAL SHOW =====================
            const modalShow = document.getElementById('modalShow');
            document.getElementById('closeShow').onclick = () => closeModal(modalShow);
            document.getElementById('closeShow2').onclick = () => closeModal(modalShow);
            modalShow.addEventListener('click', e => {
                if (e.target === modalShow) closeModal(modalShow);
            });

            const statusColorMap = {
                lunas: 'bg-green-100 text-green-700',
                partial: 'bg-yellow-100 text-yellow-700',
                overdue: 'bg-red-100 text-red-700',
                draft: 'bg-gray-100 text-gray-700',
                paid: 'bg-green-100 text-green-700',
                unpaid: 'bg-red-100 text-red-700',
            };

            document.querySelectorAll('.showBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const d = this.dataset;

                    // subtitle
                    document.getElementById('show_subtitle').textContent = d.invoice_no;

                    // Seksi 1
                    setText('show_invoice_no', d.invoice_no);
                    setText('show_invoice_date', d.invoice_date);
                    setText('show_customer_name', d.customer_name);
                    setText('show_type', d.type ? d.type.charAt(0).toUpperCase() + d.type.slice(1) : '-');
                    setText('show_customer_address', d.customer_address);
                    setText('show_telephone', d.telephone);
                    setText('show_email', d.email);
                    setText('show_contact_person', d.contact_person);

                    // Seksi 2
                    setText('show_penawaran', d.penawaran);
                    setText('show_kontrak', d.kontrak);
                    setText('show_kendaraan', d.kendaraan);

                    // Seksi 3
                    setText('show_satuan', d.satuan);
                    setText('show_pengirim', d.pengirim);
                    setText('show_ppn', (d.ppn || '0') + '%');
                    setText('show_pph', (d.pph || '0') + '%');
                    setText('show_total', formatRupiah(d.total));

                    // Seksi 4 — status badges
                    const statusEl = document.getElementById('show_status');
                    statusEl.textContent = d.status ? d.status.toUpperCase() : '-';
                    statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (statusColorMap[d
                        .status] ?? 'bg-gray-100 text-gray-700');

                    const payEl = document.getElementById('show_payment_status');
                    payEl.textContent = d.payment_status ? d.payment_status.toUpperCase() : '-';
                    payEl.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (statusColorMap[d
                        .payment_status] ?? 'bg-gray-100 text-gray-700');

                    // Seksi 5
                    setText('show_staff', d.staff);
                    setText('show_name_staff', d.name_staff);
                    setText('show_direktur', d.direktur);
                    setText('show_name_direktur', d.name_direktur);

                    const ttdStaffWrap = document.getElementById('show_ttd_staff_wrap');
                    if (d.ttd_staff) {
                        document.getElementById('show_ttd_staff_img').src = '/' + d.ttd_staff;
                        ttdStaffWrap.classList.remove('hidden');
                    } else {
                        ttdStaffWrap.classList.add('hidden');
                    }

                    const ttdDirWrap = document.getElementById('show_ttd_direktur_wrap');
                    if (d.ttd_direktur) {
                        document.getElementById('show_ttd_direktur_img').src = '/' + d.ttd_direktur;
                        ttdDirWrap.classList.remove('hidden');
                    } else {
                        ttdDirWrap.classList.add('hidden');
                    }

                    openModal(modalShow);
                });
            });

            // ===================== MODAL EDIT =====================
            const modalEdit = document.getElementById('modalEdit');
            const formEdit = document.getElementById('formEdit');
            document.getElementById('closeEdit').onclick = () => closeModal(modalEdit);
            document.getElementById('closeEdit2').onclick = () => closeModal(modalEdit);
            modalEdit.addEventListener('click', e => {
                if (e.target === modalEdit) closeModal(modalEdit);
            });

            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const d = this.dataset;

                    // Set form action
                    formEdit.action = "{{ url('admin/invoices') }}/" + d.id;

                    // Seksi 1
                    setVal('edit_invoice_no', d.invoice_no);
                    setVal('edit_invoice_date', d.invoice_date);
                    setVal('edit_customer_name', d.customer_name);
                    setVal('edit_type', d.type);
                    setVal('edit_customer_address', d.customer_address);
                    setVal('edit_telephone', d.telephone);
                    setVal('edit_email', d.email);
                    setVal('edit_contact_person', d.contact_person);

                    // Seksi 2
                    setVal('edit_penawaran_id', d.penawaran_id);
                    setVal('edit_kontrak_id', d.kontrak_id);
                    setVal('edit_kendaraan_id', d.kendaraan_id);

                    // Seksi 3
                    setVal('edit_satuan', d.satuan);
                    setVal('edit_pengirim', d.pengirim);
                    setVal('edit_ppn', d.ppn ?? 0);
                    setVal('edit_pph', d.pph ?? 0);
                    setVal('edit_total', d.total ?? 0);

                    // Seksi 4
                    setVal('edit_status', d.status ?? 'draft');
                    setVal('edit_payment_status', d.payment_status ?? 'unpaid');

                    // Seksi 5
                    setVal('edit_staff', d.staff);
                    setVal('edit_name_staff', d.name_staff);
                    setVal('edit_direktur', d.direktur);
                    setVal('edit_name_direktur', d.name_direktur);

                    document.getElementById('preview_ttd_staff').innerHTML =
                        d.ttd_staff ? `<img src="/${d.ttd_staff}" class="w-24 rounded border mt-1">` : '';
                    document.getElementById('preview_ttd_direktur').innerHTML =
                        d.ttd_direktur ? `<img src="/${d.ttd_direktur}" class="w-24 rounded border mt-1">` : '';

                    // Buka modal LANGSUNG — tanpa fetch
                    openModal(modalEdit);
                });
            });
        </script>
    @endpush

@endsection
