@extends('admin.layouts.app')

@section('title', 'Data Penawaran Kendaraan')

@section('content')
    <div class="space-y-6 p-5">

        {{-- ALERT --}}
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="fa fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        @endif

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Penawaran Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh penawaran kendaraan</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('penawaran.pdf') }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fa fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('penawaran.export.excel', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
                <button type="button" id="btnTambah"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa fa-plus text-sm"></i> Tambah Penawaran
                </button>
            </div>
        </div>

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @php
                    $navItems = [
                        ['label' => 'Summary',   'url' => '/admin/summary',   'icon' => 'bi bi-bar-chart-line'],
                        ['label' => 'Penawaran', 'url' => '/admin/penawaran', 'icon' => 'bi bi-file-earmark-richtext'],
                        ['label' => 'Kontrak',   'url' => '/admin/kontrak',   'icon' => 'bi bi-file-earmark-lock'],
                        ['label' => 'Invoice',   'url' => '/admin/invoices',  'icon' => 'bi bi-receipt-cutoff'],
                        ['label' => 'Payments',  'url' => '/admin/payments',  'icon' => 'bi bi-credit-card-2-front'],
                        ['label' => 'Reminders', 'url' => '/admin/reminders', 'icon' => 'bi bi-bell'],
                    ];
                @endphp
                @foreach ($navItems as $item)
                    @php $isActive = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*'); @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                            {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Total Penawaran</p>
                <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $penawarans->total() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Approved</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $penawarans->getCollection()->whereIn('status',['approved','active'])->count() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Pending</p>
                <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $penawarans->getCollection()->where('status','pending')->count() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Expired / Rejected</p>
                <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $penawarans->getCollection()->whereIn('status',['expired','rejected'])->count() }}</h2>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        {{-- SEARCH + TOGGLE KOLOM --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" class="flex gap-2 flex-1 flex-wrap">
                <div class="relative flex-1 min-w-[180px]">
                    <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nomor penawaran atau customer..."
                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <button class="bg-gray-800 text-white text-xs px-4 py-1.5 rounded-lg">Cari</button>
            </form>
            <div id="colToggleWrap" class="relative">
                <button type="button" onclick="toggleColDropdown()"
                    class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 bg-white hover:bg-gray-50 whitespace-nowrap">
                    <i class="bi bi-layout-three-columns"></i> Kolom <i class="bi bi-chevron-down text-[10px]"></i>
                </button>
                <div id="colDropdown"
                    class="hidden absolute right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-3 min-w-[160px] max-h-64 overflow-y-auto">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase mb-2">Tampilkan Kolom</p>
                    @foreach(['col-nopenawaran'=>'No Penawaran','col-tanggal'=>'Tanggal','col-periode'=>'Periode','col-kendaraan'=>'Kendaraan','col-status'=>'Status','col-penawaran'=>'Approve','col-aksi'=>'Aksi'] as $cid=>$clabel)
                    <label class="flex items-center gap-2 py-1 cursor-pointer hover:text-blue-600 text-xs text-gray-700">
                        <input type="checkbox" class="col-toggle" data-col="{{ $cid }}" checked onchange="toggleColumn('{{ $cid }}', this.checked)"> {{ $clabel }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th data-col="col-nopenawaran" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No Penawaran</th>
                        <th data-col="col-tanggal"     class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th data-col="col-periode"     class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Periode</th>
                        <th data-col="col-kendaraan"   class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th data-col="col-status"      class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th data-col="col-penawaran"   class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Approve</th>
                        <th data-col="col-aksi"        class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse ($penawarans as $p)
                            <tr class="border-t border-gray-50 {{ $p->isExpired ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }} transition-colors">

                            <td class="px-4 py-3.5 text-gray-400 text-xs">
                                    {{ $penawarans->firstItem() + $loop->index }}
                                </td>
                                <td class="px-4 py-3.5" data-col="col-nopenawaran">
                                    <span class="font-mono text-xs font-semibold text-blue-700">{{ $p->no_penawaran }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-600" data-col="col-tanggal">
                                    {{ optional($p->tanggal_penawaran)->format('d M Y') }}
                                </td>

                                <td class="px-4 py-3" data-col="col-periode">
                                    {{ $p->periode }} {{ $p->periode_satuan === 'hari' ? 'Hari' : 'Bulan' }}

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
                                                        @if ($p->sisaJam <= 0)
                                                            Berakhir Hari Ini
                                                        @else
                                                            Berakhir dalam {{ $p->sisaJam }} jam
                                                        @endif
                                                    @elseif ($p->sisaHari == 1)
                                                        @if ($p->periode_satuan === 'hari')
                                                            Berakhir dalam {{ $p->sisaJam }} jam
                                                        @else
                                                            Berakhir Besok
                                                        @endif
                                                    @else
                                                        Berakhir dalam {{ $p->sisaHari }} hari
                                                    @endif

                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <td class="px-4 py-3" data-col="col-kendaraan">
                                    @foreach ($p->items as $item)
                                        <div>
                                            • {{ optional($item->kendaraan)->merk }} -
                                            {{ optional($item->kendaraan)->nopol }}
                                        </div>
                                    @endforeach
                                </td>

                                <td class="px-4 py-3.5 text-center" data-col="col-status">
                                    @php
                                        $sc = match($p->status) {
                                            'approved' => 'bg-green-100 text-green-700',
                                            'active'   => 'bg-blue-100 text-blue-700',
                                            'pending'  => 'bg-yellow-100 text-yellow-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            'expired'  => 'bg-gray-100 text-gray-600',
                                            default    => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                        {{ strtoupper($p->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5" data-col="col-penawaran">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                        {{-- Download draft PDF --}}
                                        @if($p->file_penawaran)
                                            <a href="{{ asset($p->file_penawaran) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                                                <i class="fa fa-file-pdf text-xs"></i> Draft
                                            </a>
                                        @else
                                            <a href="{{ route('penawaran.download-draft', $p->id) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                                                <i class="fa fa-file-pdf text-xs"></i> Draft
                                            </a>
                                        @endif

                                        {{-- Print preview --}}
                                        <a href="{{ route('penawaran.print', $p->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors">
                                            <i class="fa fa-print text-xs"></i> Print
                                        </a>

                                        @if (!in_array($p->status, ['approved', 'rejected', 'expired']))
                                            {{-- Approve — buka modal upload --}}
                                            <button type="button"
                                                onclick="openApproveModal({{ $p->id }}, '{{ $p->no_penawaran }}')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                                                <i class="fa fa-check text-xs"></i> Approve
                                            </button>
                                            <form action="{{ route('penawaran.reject', $p->id) }}" method="POST"
                                                onsubmit="return confirm('Tolak penawaran ini?')" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                    <i class="fa fa-times text-xs"></i> Reject
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">{{ ucfirst($p->status) }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3.5" data-col="col-aksi">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="showBtn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors"
                                            data-id="{{ $p->id }}">
                                            <i class="fa fa-eye text-xs"></i>
                                        </button>
                                        <button class="editBtn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors"
                                            data-id="{{ $p->id }}">
                                            <i class="fa fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('penawaran.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="fa fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-gray-400 text-sm">
                                    <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                    Belum ada data penawaran
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100 px-5">
            <x-pagination :paginator="$penawarans" />
        </div>

        </div>{{-- end TABLE CARD --}}
    </div>{{-- end space-y-6 --}}

    {{-- ========================= MODAL TAMBAH ========================= --}}
    <div id="modalTambah" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-[95%] max-w-7xl max-h-[95vh] overflow-y-auto">
            <form id="formTambah" action="{{ route('penawaran.store') }}" method="POST">
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
                                min="{{ date('Y-m-d') }}"
                                class="w-full border rounded-lg p-2 mt-1" required>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                        <div>
                            <label>Kepada</label>
                            <input type="text" name="kepada" value="{{ old('kepada') }}"
                                placeholder="cth: PT. Maju Jaya Tbk."
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>UP</label>
                            <input type="text" name="up" value="{{ old('up') }}"
                                placeholder="cth: Bapak Budi Santoso"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label>Perihal</label>
                        <input type="text" name="perihal" value="{{ old('perihal') }}"
                            placeholder="cth: Penawaran Jasa Sewa Kendaraan Operasional"
                            class="w-full border rounded-lg p-2 mt-1">
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
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
                            <input type="text" name="name_staff"
                                placeholder="cth: Agus Pratama"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Masa Penawaran</label>
                            <div class="flex mt-1">
                                <input type="number" name="periode" placeholder="12"
                                    class="w-full border border-r-0 rounded-l-lg p-2">
                                <select name="periode_satuan"
                                    class="border border-l-0 rounded-r-lg px-3 py-2 bg-gray-100 text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                    <option value="bulan">Bulan</option>
                                    <option value="hari">Hari</option>
                                </select>
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

                    <hr class="my-6">

                    {{-- ── KETENTUAN EDITOR (TAMBAH) ── --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-gray-700">Ketentuan</h4>
                            <button type="button" onclick="resetKetentuanTambah()"
                                class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50">
                                <i class="fa fa-rotate-left"></i> Reset Default
                            </button>
                        </div>
                        <div class="text-xs text-gray-400 mb-2">
                            <i class="fa fa-info-circle"></i>
                            Baris baru = poin baru. Baris diawali <code class="bg-gray-100 px-1 rounded">-</code> = sub-item dari poin di atasnya.
                        </div>
                        <textarea
                            name="ketentuan_plain"
                            id="tambahKetentuanText"
                            placeholder="Tulis ketentuan di sini..."
                            class="w-full border rounded-lg p-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            style="height:300px; resize:vertical; overflow-y:auto;"></textarea>
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
                                min="{{ date('Y-m-d') }}"
                                class="w-full border rounded-lg p-2 mt-1" required>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-5">
                        <div>
                            <label>Kepada</label>
                            <input id="edit_kepada" type="text" name="kepada"
                                placeholder="cth: PT. Maju Jaya Tbk."
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>UP</label>
                            <input id="edit_up" type="text" name="up"
                                placeholder="cth: Bapak Budi Santoso"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label>Perihal</label>
                        <input id="edit_perihal" type="text" name="perihal"
                            placeholder="cth: Penawaran Jasa Sewa Kendaraan Operasional"
                            class="w-full border rounded-lg p-2 mt-1">
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-3 gap-4">
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
                                placeholder="cth: Agus Pratama"
                                class="w-full border rounded-lg p-2 mt-1">
                        </div>
                        <div>
                            <label>Periode</label>
                            <div class="flex mt-1">
                                <input id="edit_periode" type="number" name="periode"
                                    placeholder="cth: 12"
                                    class="w-full border border-r-0 rounded-l-lg p-2">
                                <select id="edit_periode_satuan" name="periode_satuan"
                                    class="border border-l-0 rounded-r-lg px-3 py-2 bg-gray-100 text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                    <option value="bulan">Bulan</option>
                                    <option value="hari">Hari</option>
                                </select>
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

                    <hr class="my-6">

                    {{-- ── KETENTUAN EDITOR (EDIT) ── --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-gray-700">Ketentuan</h4>
                            <button type="button" onclick="resetKetentuanEdit()"
                                class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50">
                                <i class="fa fa-rotate-left"></i> Reset Default
                            </button>
                        </div>
                        <div class="text-xs text-gray-400 mb-2">
                            <i class="fa fa-info-circle"></i>
                            Baris baru = poin baru. Baris diawali <code class="bg-gray-100 px-1 rounded">-</code> = sub-item dari poin di atasnya.
                        </div>
                        <textarea
                            name="ketentuan_plain"
                            id="editKetentuanText"
                            placeholder="Tulis ketentuan di sini..."
                            class="w-full border rounded-lg p-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            style="height:300px; resize:vertical; overflow-y:auto;"></textarea>
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
                        <label class="text-sm font-medium text-gray-500">No KTP</label>
                        <p id="show_no_ktp" class="text-gray-800 mt-1">-</p>
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
                        <label class="text-sm font-medium text-gray-500">Jenis Pelanggan</label>
                        <p id="show_jenis_pelanggan" class="text-gray-800 mt-1">-</p>
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
                <select name="kendaraan_id[]" class="kendaraan w-full border rounded p-2" required
                    onchange="onTambahKendaraanChange(this)">
                    <option value="">Pilih Kendaraan</option>
                    @foreach ($kendaraans as $k)
                        <option value="{{ $k->id }}"
                            data-tahun="{{ $k->tahun_pembuatan ?? '' }}"
                            data-harga="{{ (int)($k->harga_sewa_per_hari ?? 0) }}">
                            {{ $k->merk }} - {{ $k->nopol }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="border p-2">
                <input type="number" name="qty[]" value="1" min="1"
                    class="qty w-full border rounded p-2 bg-gray-50" readonly>
            </td>
            <td class="border p-2">
                <input type="text" name="tahun_unit[]" value="" placeholder="—"
                    class="tahun-unit w-full border rounded p-2 bg-gray-50" readonly>
            </td>
            <td class="border p-2">
                <input type="number" name="price[]" value="0"
                    class="price w-full border rounded p-2 bg-gray-50" readonly>
            </td>
            <td class="border p-2">
                <input type="number" name="durasi[]" value="1" min="1"
                    class="durasi w-full border rounded p-2"
                    oninput="onTambahDurasiChange(this)">
            </td>
            <td class="border p-2">
                <select name="satuan_durasi[]" class="satuan w-full border rounded p-2"
                    onchange="onTambahSatuanChange(this)">
                    <option value="Hari">Hari</option>
                    <option value="Bulan" selected>Bulan</option>
                    <option value="Tahun">Tahun</option>
                </select>
            </td>
            <td class="border p-2 text-center">
                <button type="button" class="hapusItem bg-red-600 text-white px-3 py-1 rounded"
                    onclick="refreshAllSelects(document.getElementById('itemContainer'))">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
        <script>

            // ========================= KETENTUAN HELPERS (textarea) =========================
            const DEFAULT_KETENTUAN = [
                { teks: 'Harga sewa termasuk PPN 11%, diluar BBM, Tol dan Parkir', sub: [] },
                { teks: 'TOP (Term of payment) min. 2 minggu setelah pengiriman kendaraan dan invoice diterima', sub: [] },
                { teks: 'Pembatalan kontrak di kenakan penalty sebesar 25% dari sisa nilai kontrak sewa kendaraan', sub: [] },
                { teks: 'Klaim own risk untuk kerusakan kendaraan sebesar Rp. 350.000,- / kejadian', sub: [] },
                { teks: 'Klaim own risk untuk kehilangan kendaraan sebesar 10% dari nilai pertanggungan', sub: [] },
                { teks: 'Harga penawaran ini berlaku selama 2 (dua) minggu sejak tanggal penawaran', sub: [] },
                { teks: 'Pengiriman Kendaraan 4 (Empat) minggu setelah PO / SPK diterima', sub: [] },
                { teks: 'Harga sudah termasuk :', sub: [
                    'Perawatan kendaraan (Maintenance, Sparepart, & Penggantian ban bisa dilakukan di tahun ke-3)',
                    'Asuransi All Risk (TJH max. 10 jt)',
                    'Kendaraan pengganti sementara',
                    'Perpanjangan STNK dan KIR',
                ]},
            ];

            /**
             * Konversi array [{teks, sub[]}] ke string multi-baris untuk textarea.
             * Sub-item diawali "- ".
             */
            function ketentuanToText(items) {
                if (!Array.isArray(items)) return '';
                const lines = [];
                items.forEach(item => {
                    const teks = (typeof item === 'string') ? item : (item.teks ?? '');
                    const sub  = (typeof item === 'string') ? [] : (item.sub ?? []);
                    if (teks) lines.push(teks);
                    sub.forEach(s => { if (s) lines.push('- ' + s); });
                });
                return lines.join('\n');
            }

            /**
             * Isi textarea dengan data ketentuan (array).
             */
            function renderKetentuanToTextarea(textareaId, items) {
                const el = document.getElementById(textareaId);
                if (el) el.value = ketentuanToText(items);
            }

            function resetKetentuanTambah() { renderKetentuanToTextarea('tambahKetentuanText', DEFAULT_KETENTUAN); }
            function resetKetentuanEdit()   { renderKetentuanToTextarea('editKetentuanText',   DEFAULT_KETENTUAN); }
            // ========================= END KETENTUAN HELPERS =========================

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
            const kendaraanOptions = @json($kendaraanJson);

            function buildKendaraanSelect(selectedId = '', excludeIds = []) {
                let opts = '<option value="">Pilih Kendaraan</option>';
                kendaraanOptions.forEach(k => {
                    const disabled = excludeIds.includes(String(k.id)) && String(k.id) !== String(selectedId);
                    opts += `<option value="${k.id}" data-tahun="${k.tahun}" data-harga="${k.harga}" ${k.id == selectedId ? 'selected' : ''} ${disabled ? 'disabled' : ''}>${k.nama}</option>`;
                });
                return opts;
            }

            // Hitung multiplier durasi → hari
            function getMultiplier(satuan, durasi) {
                const d = Number(durasi) || 0;
                if (satuan === 'Hari')  return d;
                if (satuan === 'Bulan') return d * 30;
                if (satuan === 'Tahun') return d * 365;
                return d;
            }

            // Harga selalu dihitung untuk 1 Bulan (30 hari), durasi tidak mempengaruhi harga
            function kalkulasiHarga(kendaraanId, durasi, satuan) {
                const kend = kendaraanOptions.find(k => String(k.id) === String(kendaraanId));
                if (!kend) return 0;
                return kend.harga * 30; // harga per bulan = harga_sewa_per_hari × 30
            }

            // Kumpulkan semua kendaraan_id yang sudah dipakai di container
            function getUsedIds(container, exceptRow = null) {
                const ids = [];
                container.querySelectorAll('tr').forEach(row => {
                    if (row === exceptRow) return;
                    const sel = row.querySelector('.kendaraan');
                    if (sel && sel.value) ids.push(String(sel.value));
                });
                return ids;
            }

            // Refresh semua select di container agar opsi duplikat di-disable
            function refreshAllSelects(container) {
                container.querySelectorAll('tr').forEach(row => {
                    const sel = row.querySelector('.kendaraan');
                    if (!sel) return;
                    const used = getUsedIds(container, row);
                    const currentVal = sel.value;
                    sel.innerHTML = buildKendaraanSelect(currentVal, used);
                });
            }

            function buildSatuanSelect(selected = '') {
                return ['Hari', 'Bulan', 'Tahun'].map(s =>
                    `<option value="${s}" ${s === selected ? 'selected' : ''}>${s}</option>`
                ).join('');
            }

            function buildTahunSelect(selected = '') {
                const currentYear = new Date().getFullYear();
                let opts = '<option value="">-- Pilih Tahun --</option>';
                for (let y = currentYear; y >= 1980; y--) {
                    opts += `<option value="${y}" ${String(y) === String(selected) ? 'selected' : ''}>${y}</option>`;
                }
                return opts;
            }

            function buildEditRow(item = {}) {
                const kend = kendaraanOptions.find(k => String(k.id) === String(item.kendaraan_id ?? ''));
                const tahun = kend ? kend.tahun : (item.tahun_unit ?? '');
                const satuan = item.satuan_durasi ?? 'Bulan';
                const durasi = item.durasi ?? 1;
                const harga  = kend ? kalkulasiHarga(kend.id, durasi, satuan) : (item.price ?? 0);
                return `
            <tr>
                <td class="border p-2">
                    <select name="kendaraan_id[]" class="kendaraan w-full border rounded p-2" required onchange="onEditKendaraanChange(this)">
                        ${buildKendaraanSelect(item.kendaraan_id ?? '')}
                    </select>
                </td>
                <td class="border p-2">
                    <input type="number" name="qty[]" value="1" min="1" class="qty w-full border rounded p-2 bg-gray-50" readonly>
                </td>
                <td class="border p-2">
                    <input type="text" name="tahun_unit[]" value="${tahun}" class="tahun-unit w-full border rounded p-2 bg-gray-50" readonly>
                </td>
                <td class="border p-2">
                    <input type="number" name="price[]" value="${harga}" class="price w-full border rounded p-2 bg-gray-50" readonly>
                </td>
                <td class="border p-2">
                    <input type="number" name="durasi[]" value="${durasi}" min="1" class="durasi w-full border rounded p-2" oninput="onEditDurasiChange(this)">
                </td>
                <td class="border p-2">
                    <select name="satuan_durasi[]" class="satuan w-full border rounded p-2" onchange="onEditSatuanChange(this)">
                        ${buildSatuanSelect(satuan)}
                    </select>
                </td>
                <td class="border p-2 text-center">
                    <button type="button" class="hapusEdit bg-red-600 text-white px-3 py-1 rounded" onclick="refreshAllSelects(document.getElementById('editItemContainer'))">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            }

            function onEditKendaraanChange(sel) {
                const row = sel.closest('tr');
                const container = document.getElementById('editItemContainer');
                const kend = kendaraanOptions.find(k => String(k.id) === sel.value);
                row.querySelector('.tahun-unit').value = kend ? kend.tahun : '';
                updateRowPrice(row);
                refreshAllSelects(container);
                hitungEditTotal();
            }

            function onEditDurasiChange(input) {
                const row = input.closest('tr');
                updateRowPrice(row);
                hitungEditTotal();
            }

            function onEditSatuanChange(sel) {
                const row = sel.closest('tr');
                updateRowPrice(row);
                hitungEditTotal();
            }

            function updateRowPrice(row) {
                const kendaraanId = row.querySelector('.kendaraan')?.value;
                const durasi  = row.querySelector('.durasi')?.value  || 1;
                const satuan  = row.querySelector('.satuan')?.value  || 'Hari';
                const price   = kalkulasiHarga(kendaraanId, durasi, satuan);
                const priceEl = row.querySelector('.price');
                if (priceEl) priceEl.value = price;
            }

            // ---- MODAL TAMBAH ----
            const modalTambah = document.getElementById('modalTambah');
            const itemContainer = document.getElementById('itemContainer');
            const template = document.getElementById('itemTemplate');

            document.getElementById('btnTambah').onclick = () => {
                modalTambah.classList.remove('hidden');
                modalTambah.classList.add('flex');
                // Init ketentuan default jika textarea masih kosong
                const kTextarea = document.getElementById('tambahKetentuanText');
                if (kTextarea && kTextarea.value.trim() === '') {
                    renderKetentuanToTextarea('tambahKetentuanText', DEFAULT_KETENTUAN);
                }
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

            // Auto-reopen modal tambah on validation error
            @if ($errors->any() && !session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                modalTambah.classList.remove('hidden');
                modalTambah.classList.add('flex');
            });
            @endif

            function tambahBaris() {
                itemContainer.appendChild(template.content.cloneNode(true));
                // Refresh disabled options setelah baris baru ditambahkan
                refreshAllSelects(itemContainer);
                hitungTotal();
            }
            document.getElementById('btnTambahItem').onclick = tambahBaris;

            // Tambah modal: kendaraan change → auto-fill tahun & harga
            function onTambahKendaraanChange(sel) {
                const row = sel.closest('tr');
                const kend = kendaraanOptions.find(k => String(k.id) === String(sel.value));
                row.querySelector('.tahun-unit').value = kend ? kend.tahun : '';
                updateTambahRowPrice(row);
                refreshAllSelects(itemContainer);
                hitungTotal();
            }

            function onTambahDurasiChange(input) {
                const row = input.closest('tr');
                updateTambahRowPrice(row);
                hitungTotal();
            }

            function onTambahSatuanChange(sel) {
                const row = sel.closest('tr');
                updateTambahRowPrice(row);
                hitungTotal();
            }

            function updateTambahRowPrice(row) {
                const kendaraanId = row.querySelector('.kendaraan')?.value;
                const durasi  = row.querySelector('.durasi')?.value  || 1;
                const satuan  = row.querySelector('.satuan')?.value  || 'Bulan';
                const price   = kalkulasiHarga(kendaraanId, durasi, satuan);
                const priceEl = row.querySelector('.price');
                if (priceEl) priceEl.value = price;
            }

            itemContainer.addEventListener('click', function(e) {
                if (e.target.closest('.hapusItem')) {
                    e.target.closest('tr').remove();
                    refreshAllSelects(itemContainer);
                    hitungTotal();
                }
            });

            function hitungTotal() {
                let total = 0;
                document.querySelectorAll('#itemContainer tr').forEach(row => {
                    total += Number(row.querySelector('.price')?.value ?? 0);
                });
                document.getElementById('grandTotal').value = total.toLocaleString('id-ID');
            }

            tambahBaris();
            // Initial refresh after first auto-added row
            refreshAllSelects(itemContainer);

            // Validasi: semua select kendaraan wajib dipilih sebelum submit
            function validateKendaraanItems(containerSelector, formEl) {
                if (!formEl) return true;
                formEl.addEventListener('submit', function(e) {
                    const selects = document.querySelectorAll(containerSelector + ' .kendaraan');
                    let hasEmpty = false;
                    selects.forEach(function(sel) {
                        if (!sel.value) {
                            sel.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                            hasEmpty = true;
                        } else {
                            sel.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                        }
                    });
                    if (hasEmpty) {
                        e.preventDefault();
                        alert('Pilih kendaraan untuk semua baris item terlebih dahulu.');
                    }
                });
            }
            validateKendaraanItems('#itemContainer', document.getElementById('formTambah'));
            validateKendaraanItems('#editItemContainer', document.getElementById('formEdit'));

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
                            document.getElementById('edit_staff').value = data.staff ?? '';
                            document.getElementById('edit_name_staff').value = data.name_staff ?? '';
                            document.getElementById('edit_periode').value = data.periode ?? '';
                            document.getElementById('edit_periode_satuan').value = data.periode_satuan ?? 'bulan';

                            loadEditItems(data.items ?? []);

                            // Load ketentuan ke textarea — pakai default jika kosong di DB
                            const ketentuanData = (data.ketentuan && data.ketentuan.length)
                                ? data.ketentuan
                                : DEFAULT_KETENTUAN;
                            renderKetentuanToTextarea('editKetentuanText', ketentuanData);
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
                    refreshAllSelects(editItemContainer);
                    hitungEditTotal();
                }
            });

            function hitungEditTotal() {
                let total = 0;
                document.querySelectorAll('#editItemContainer tr').forEach(row => {
                    total += Number(row.querySelector('.price')?.value ?? 0);
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
                            document.getElementById('show_no_ktp').textContent = data.no_ktp ?? '-';
                            document.getElementById('show_contact').textContent = data.contact_person ??
                            '-';
                            document.getElementById('show_email').textContent = data.email_person ?? '-';
                            document.getElementById('show_alamat').textContent = data.alamat ?? '-';
                            document.getElementById('show_jenis_pelanggan').textContent = data.jenis_pelanggan ??
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

    {{-- ========================= AUTOSUGGEST CUSTOMER ========================= --}}
    <script>
        const customerSearchUrl = "{{ route('penawaran.customer-search') }}";
        let customerSearchTimer = null;

        function setupCustomerAutosuggest(inputEl, listEl, noKtpEl, alamatEl, jenisEl, emailEl, contactEl) {
            inputEl.addEventListener('input', function () {
                clearTimeout(customerSearchTimer);
                const q = this.value.trim();
                if (q.length < 1) { listEl.classList.add('hidden'); return; }
                customerSearchTimer = setTimeout(() => {
                    fetch(customerSearchUrl + '?q=' + encodeURIComponent(q))
                        .then(r => r.json())
                        .then(results => {
                            listEl.innerHTML = '';
                            if (!results.length) { listEl.classList.add('hidden'); return; }
                            results.forEach(member => {
                                const li = document.createElement('li');
                                li.className = 'px-3 py-2 cursor-pointer hover:bg-blue-50 text-gray-700';
                                li.textContent = member.nama_pelanggan;
                                li.addEventListener('mousedown', function (e) {
                                    e.preventDefault();
                                    inputEl.value = member.nama_pelanggan;
                                    if (noKtpEl)   noKtpEl.value   = member.no_ktp           ?? '';
                                    if (alamatEl)  alamatEl.value  = member.alamat            ?? '';
                                    if (jenisEl)   jenisEl.value   = member.jenis_pelanggan   ?? '';
                                    if (emailEl)   emailEl.value   = member.email_pelanggan   ?? '';
                                    if (contactEl) contactEl.value = member.kontak_pelanggan  ?? '';
                                    listEl.classList.add('hidden');
                                });
                                listEl.appendChild(li);
                            });
                            listEl.classList.remove('hidden');
                        })
                        .catch(() => listEl.classList.add('hidden'));
                }, 300);
            });
            inputEl.addEventListener('blur', function () {
                setTimeout(() => listEl.classList.add('hidden'), 200);
            });
            inputEl.addEventListener('focus', function () {
                if (this.value.trim().length > 0) this.dispatchEvent(new Event('input'));
            });
        }

        // Autosuggest customer diaktifkan di modal approve kontrak
    </script>

    @endpush

{{-- ========================= MODAL APPROVE ========================= --}}
<div id="modalApprove" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Approve Penawaran</h2>
                <p id="approveModalSubtitle" class="text-xs text-gray-400 mt-0.5">Upload file yang sudah ditandatangani</p>
            </div>
            <button type="button" onclick="closeApproveModal()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="formApprove" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700">
                <i class="fa fa-info-circle mr-1"></i>
                Pastikan file PDF penawaran sudah ditandatangani oleh kedua pihak sebelum di-approve.
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    File Penawaran Ditandatangani <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file_penawaran" accept=".pdf" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-green-400">
                <p class="text-xs text-gray-400 mt-1">Format: PDF saja. Maks 10MB.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeApproveModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    <i class="fa fa-check text-xs"></i> Approve
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id, noPenawaran) {
        document.getElementById('approveModalSubtitle').textContent = noPenawaran;
        document.getElementById('formApprove').action = '/admin/penawaran/' + id + '/approve';
        document.getElementById('modalApprove').classList.remove('hidden');
        document.getElementById('modalApprove').classList.add('flex');
    }
    function closeApproveModal() {
        document.getElementById('modalApprove').classList.add('hidden');
        document.getElementById('modalApprove').classList.remove('flex');
    }
    // Close on backdrop click
    document.getElementById('modalApprove').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });
</script>

@endsection

