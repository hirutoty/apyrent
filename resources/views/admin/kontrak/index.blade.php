@extends('admin.layouts.app')

@section('title', 'Data Kontrak Kendaraan')

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
                <h1 class="text-2xl font-bold text-gray-800">Data Kontrak Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data kontrak penawaran</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('kontrak.pdf', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fa fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('kontrak.export.excel', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
                <button onclick="openModal('modalCreate')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa fa-plus text-sm"></i> Tambah Kontrak
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
                <p class="text-sm text-gray-500">Total Kontrak</p>
                <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $kontraks->total() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Active</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $kontraks->getCollection()->whereIn('status',['active','approved'])->count() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Pending</p>
                <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $kontraks->getCollection()->where('status','pending')->count() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Expired / Terminated</p>
                <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $kontraks->getCollection()->whereIn('status',['expired','terminated','rejected'])->count() }}</h2>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

            {{-- SEARCH + EXPORT + TOGGLE KOLOM --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" class="flex gap-2 flex-1 flex-wrap">
                    <div class="relative flex-1 min-w-[180px]">
                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari no kontrak / pihak..."
                            class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <button class="bg-gray-800 text-white text-xs px-4 py-1.5 rounded-lg">Cari</button>
                </form>
                <div class="relative" id="colToggleWrap">
                    <button type="button" onclick="toggleColDropdown()"
                        class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 bg-white hover:bg-gray-50 whitespace-nowrap">
                        <i class="bi bi-layout-three-columns"></i> Kolom <i class="bi bi-chevron-down text-[10px]"></i>
                    </button>
                    <div id="colDropdown"
                        class="hidden absolute right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-3 min-w-[160px] max-h-64 overflow-y-auto">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase mb-2">Tampilkan Kolom</p>
                        @foreach([
                            'col-nokontrak'  => 'No Kontrak',
                            'col-penawaran'  => 'Penawaran',
                            'col-kendaraan'  => 'Kendaraan',
                            'col-tanggal'    => 'Tanggal',
                            'col-perjanjian' => 'Perjanjian',
                            'col-status'     => 'Status',
                            'col-aksi'       => 'Aksi',
                        ] as $cid => $clabel)
                        <label class="flex items-center gap-2 py-1 cursor-pointer hover:text-blue-600 text-xs text-gray-700">
                            <input type="checkbox" checked onchange="toggleCol('{{ $cid }}', this.checked)" class="rounded">
                            {{ $clabel }}
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
                            <th data-col="col-nokontrak"  class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No Kontrak</th>
                            <th data-col="col-penawaran"  class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Penawaran</th>
                            <th data-col="col-kendaraan"  class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                            <th data-col="col-kendaraan"  class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                            <th data-col="col-tanggal"    class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                            <th data-col="col-perjanjian" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Perjanjian Pembayaran</th>
                            <th data-col="col-status"     class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                            <th data-col="col-aksi"       class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kontraks as $no => $k)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                                <td class="px-4 py-3.5 text-xs text-gray-400">{{ $no + 1 }}</td>

                                <td class="px-4 py-3.5" data-col="col-nokontrak">
                                    <span class="font-mono text-xs font-semibold text-blue-700">{{ $k->no_kontrak }}</span>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-600" data-col="col-penawaran">
                                    {{ $k->penawaran->no_penawaran ?? '�' }}
                                </td>

                                {{-- Task 3: Kolom Kendaraan --}}
                                <td class="px-4 py-3.5" data-col="col-kendaraan">
                                    @if ($k->penawaran && $k->penawaran->items->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach ($k->penawaran->items as $item)
                                                @if ($item->kendaraan)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-semibold text-gray-800">{{ $item->kendaraan->merk }} -
                                                        {{ $item->kendaraan->nopol }}</span>
                                                        
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td>
                                                             @if ($k->penawaran && $k->penawaran->items->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach ($k->penawaran->items as $item)
                                                @if ($item->kendaraan)
                                                    <div class="flex items-center gap-1.5">
                                                        
                                                        @php
                                                            $stKend = $item->kendaraan->status_kendaraan ?? '';
                                                            $stColor = match($stKend) {
                                                                'tersedia' => 'bg-green-100 text-green-700',
                                                                'disewa'   => 'bg-blue-100 text-blue-700',
                                                                default    => 'bg-gray-100 text-gray-600',
                                                            };
                                                        @endphp
                                                        <span class="inline-flex items-center justify-center text-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold {{ $stColor }}">
                                                            {{ ucfirst($stKend) ?: '-' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif

                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-600" data-col="col-tanggal">
                                    {{ $k->tanggal_kontrak?->format('d M Y') }}
                                </td>

                                <td class="px-4 py-3.5" data-col="col-perjanjian">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm text-gray-700">{{ $k->perjanjian_pembayaran?->format('d M Y') }}</span>
                                        @if ($k->showReminder && $k->isExpired)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Terlambat {{ abs($k->sisaHari) }} hari
                                            </span>
                                        @elseif ($k->showReminder && $k->isSoon)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                @if ($k->sisaHari == 0) Jatuh Tempo Hari Ini
                                                @elseif ($k->sisaHari == 1) Jatuh Tempo Besok
                                                @else Berakhir {{ $k->sisaHari }} hari lagi
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center" data-col="col-status">
                                    @php
                                        $sc = match($k->status) {
                                            'active'     => 'bg-green-100 text-green-700',
                                            'approved'   => 'bg-indigo-100 text-indigo-700',
                                            'completed'  => 'bg-blue-100 text-blue-700',
                                            'pending'    => 'bg-yellow-100 text-yellow-700',
                                            'rejected'   => 'bg-red-100 text-red-600',
                                            'expired'    => 'bg-gray-100 text-gray-600',
                                            'terminated' => 'bg-gray-800 text-white',
                                            default      => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                        {{ ucfirst($k->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5" data-col="col-aksi">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick='openDetailModal(@json($k->load("penawaran.items.kendaraan")))'
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                            <i class="fa fa-eye text-xs"></i>
                                        </button>
                                        <button onclick='openEditModal(@json($k))'
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('kontrak.destroy', $k->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Hapus kontrak ini?')">
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
                                    Belum ada data kontrak
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="py-3 border-t border-gray-100"><x-pagination :paginator="$kontraks" /></div>

        </div>{{-- end TABLE CARD --}}
    </div>{{-- end space-y-6 --}}

            {{-- ================= MODAL CREATE KONTRAK ================= --}}
            <div id="modalCreate" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[95vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h2 class="text-base font-bold text-gray-800">Tambah Kontrak</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Isi data kontrak dengan lengkap</p>
                        </div>
                        <button onclick="closeModal('modalCreate')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
                    </div>
                    <form action="{{ route('kontrak.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                            <select name="penawaran_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                                <option value="">-- Pilih Penawaran --</option>
                                @foreach ($penawarans as $p)
                                    <option value="{{ $p->id }}" {{ old('penawaran_id') == $p->id ? 'selected' : '' }}>{{ $p->no_penawaran ?? 'Penawaran #' . $p->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Kontrak</label>
                            <input type="date" name="tanggal_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('tanggal_kontrak') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perjanjian Pembayaran</label>
                            <input type="date" name="perjanjian_pembayaran" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('perjanjian_pembayaran') }}">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Pertama</label>
                                <input type="text" name="pihak_pertama" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('pihak_pertama') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak 1</label>
                                <input type="text" inputmode="numeric" name="contact_pertama" maxlength="15" oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('contact_pertama') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Kedua</label>
                                <input type="text" name="pihak_kedua" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('pihak_kedua') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak 2</label>
                                <input type="text" inputmode="numeric" name="contact_kedua" maxlength="15" oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('contact_kedua') }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Kontrak</label>
                            <input type="file" name="file_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Persyaratan</label>
                            <input type="file" name="file_persyaratan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" onclick="closeModal('modalCreate')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl"><i class="fa fa-save text-xs"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= MODAL EDIT KONTRAK ================= --}}
            <div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-auto">
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[95vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h2 class="text-base font-bold text-gray-800">Edit Kontrak</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Perbarui data kontrak</p>
                        </div>
                        <button onclick="closeModal('modalEdit')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
                    </div>
                    <form id="editForm" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                            <select name="penawaran_id" id="edit_penawaran_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                                @foreach ($penawarans as $p)
                                    <option value="{{ $p->id }}" {{ old('penawaran_id') == $p->id ? 'selected' : '' }}>{{ $p->no_penawaran ?? 'Penawaran #' . $p->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Kontrak</label>
                            <input type="text" name="no_kontrak" id="edit_no_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('no_kontrak') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Kontrak</label>
                            <input type="date" name="tanggal_kontrak" id="edit_tanggal_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('tanggal_kontrak') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perjanjian Pembayaran</label>
                            <input type="date" name="perjanjian_pembayaran" id="edit_perjanjian_pembayaran" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('perjanjian_pembayaran') }}">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Pertama</label>
                                <input type="text" name="pihak_pertama" id="edit_pihak_pertama" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('pihak_pertama') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak 1</label>
                                <input type="text" inputmode="numeric" name="contact_pertama" id="edit_contact_pertama" maxlength="15" oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('contact_pertama') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Kedua</label>
                                <input type="text" name="pihak_kedua" id="edit_pihak_kedua" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required value="{{ old('pihak_kedua') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak 2</label>
                                <input type="text" inputmode="numeric" name="contact_kedua" id="edit_contact_kedua" maxlength="15" oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('contact_kedua') }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                            <select name="status" id="edit_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <p class="text-xs text-gray-400">File baru akan menggantikan file lama (opsional)</p>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Kontrak</label>
                            <input type="file" name="file_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Persyaratan</label>
                            <input type="file" name="file_persyaratan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl"><i class="fa fa-save text-xs"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= MODAL DETAIL KONTRAK ================= --}}
            <div id="modalDetail" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h2 class="text-base font-bold text-gray-800">Detail Kontrak</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Informasi lengkap kontrak</p>
                        </div>
                        <button onclick="closeModal('modalDetail')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
                    </div>
                    <div class="px-6 py-5 space-y-4 text-sm">

                        {{-- Info Utama --}}
                        <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                            <div><p class="text-xs text-gray-400">No Kontrak</p><p id="d_no_kontrak" class="font-semibold text-gray-800 mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Penawaran</p><p id="d_penawaran" class="text-gray-700 mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Tanggal Kontrak</p><p id="d_tanggal" class="text-gray-700 mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Status</p><p id="d_status" class="mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Pihak 1</p><p id="d_pihak1" class="text-gray-700 mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Kontak 1</p><p id="d_contact1" class="text-gray-700 mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Pihak 2</p><p id="d_pihak2" class="text-gray-700 mt-0.5">-</p></div>
                            <div><p class="text-xs text-gray-400">Kontak 2</p><p id="d_contact2" class="text-gray-700 mt-0.5">-</p></div>
                        </div>

                        {{-- Kendaraan --}}
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Kendaraan</p>
                            <div id="d_kendaraan_list" class="flex flex-col gap-2">
                                <p class="text-xs text-gray-400">-</p>
                            </div>
                        </div>

                        {{-- File --}}
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">File Dokumen</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div id="d_file_kontrak_wrap">
                                    <p class="text-xs text-gray-400 mb-1">File Kontrak</p>
                                    <span class="text-xs text-gray-400">Tidak ada file</span>
                                </div>
                                <div id="d_file_persyaratan_wrap">
                                    <p class="text-xs text-gray-400 mb-1">File Persyaratan</p>
                                    <span class="text-xs text-gray-400">Tidak ada file</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4 flex justify-end">
                        <button onclick="closeModal('modalDetail')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Tutup</button>
                    </div>
                </div>
            </div>


    <script>
        // -- Toggle Kolom --
        function toggleColDropdown() {
            document.getElementById('colDropdown').classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('colToggleWrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('colDropdown').classList.add('hidden');
            }
        });
        function toggleCol(colId, show) {
            document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => {
                el.style.display = show ? '' : 'none';
            });
        }

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

            // Status badge
            const statusColors = {
                active:     'bg-green-100 text-green-700',
                approved:   'bg-indigo-100 text-indigo-700',
                completed:  'bg-blue-100 text-blue-700',
                pending:    'bg-yellow-100 text-yellow-700',
                rejected:   'bg-red-100 text-red-600',
                expired:    'bg-gray-100 text-gray-600',
                terminated: 'bg-gray-800 text-white',
            };
            const st = data.status ?? '-';
            const stClass = statusColors[st] ?? 'bg-gray-100 text-gray-600';
            document.getElementById('d_status').innerHTML =
                `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${stClass}">${st.charAt(0).toUpperCase() + st.slice(1)}</span>`;

            // Kendaraan list
            const kList = document.getElementById('d_kendaraan_list');
            const items = data.penawaran?.items ?? [];
            if (items.length === 0) {
                kList.innerHTML = '<p class="text-xs text-gray-400">Tidak ada kendaraan</p>';
            } else {
                const stKendColors = {
                    tersedia: 'bg-green-100 text-green-700',
                    disewa:   'bg-blue-100 text-blue-700',
                };
                kList.innerHTML = items.map(item => {
                    const k = item.kendaraan;
                    if (!k) return '';
                    const kSt = k.status_kendaraan ?? '';
                    const kStClass = stKendColors[kSt] ?? 'bg-gray-100 text-gray-600';
                    const kStLabel = kSt ? kSt.charAt(0).toUpperCase() + kSt.slice(1) : '-';
                    return `<div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 border border-gray-100">
                        <i class="fa fa-car text-gray-400 text-xs"></i>
                        <span class="text-xs font-semibold text-gray-800">${k.merk ?? '-'}</span>
                        <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">${k.nopol ?? '-'}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold ${kStClass}">${kStLabel}</span>
                    </div>`;
                }).join('');
            }

            // Helper: ambil nama file asli (hapus prefix timestamp_ di depan)
            function getOriginalName(path) {
                if (!path) return null;
                const parts = path.split('/');
                const raw = parts[parts.length - 1];           // e.g. "1234567890_dokumen.pdf"
                const idx = raw.indexOf('_');
                return idx !== -1 ? raw.substring(idx + 1) : raw;
            }

            // File Kontrak
            const fkWrap = document.getElementById('d_file_kontrak_wrap');
            if (data.file_kontrak) {
                const fname = getOriginalName(data.file_kontrak);
                fkWrap.innerHTML = `<p class="text-xs text-gray-400 mb-1">File Kontrak</p>
                    <a href="/${data.file_kontrak}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors max-w-full">
                        <i class="fa fa-file-alt text-xs flex-shrink-0"></i>
                        <span class="truncate">${fname}</span>
                    </a>`;
            } else {
                fkWrap.innerHTML = `<p class="text-xs text-gray-400 mb-1">File Kontrak</p>
                    <span class="text-xs text-gray-400">Tidak ada file</span>`;
            }

            // File Persyaratan
            const fpWrap = document.getElementById('d_file_persyaratan_wrap');
            if (data.file_persyaratan) {
                const fname = getOriginalName(data.file_persyaratan);
                fpWrap.innerHTML = `<p class="text-xs text-gray-400 mb-1">File Persyaratan</p>
                    <a href="/${data.file_persyaratan}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors max-w-full">
                        <i class="fa fa-file-alt text-xs flex-shrink-0"></i>
                        <span class="truncate">${fname}</span>
                    </a>`;
            } else {
                fpWrap.innerHTML = `<p class="text-xs text-gray-400 mb-1">File Persyaratan</p>
                    <span class="text-xs text-gray-400">Tidak ada file</span>`;
            }
        }
    
        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') openModalTambah();
            else if (typeof openModal === 'function') openModal();
        });
        @endif
</script>
@endsection
