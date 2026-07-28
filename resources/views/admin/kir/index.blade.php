@extends('admin.layouts.app')

@section('title', 'Uji Kendaraan Bermotor')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @php
        $jumlahKir      = $data->total();
        $jumlahAktif    = $data->where('masa_berlaku', '>=', now()->toDateString())->count();
        $jumlahSegera   = $data->filter(fn($d) => \Carbon\Carbon::parse($d->masa_berlaku)->between(now(), now()->addDays(30)))->count();
        $jumlahNonaktif = $data->where('masa_berlaku', '<', now()->toDateString())->count();
        $totalBiayaKir  = $data->sum('biaya');
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data KIR</h1>
                <p class="text-sm text-gray-500 mt-0.5">Uji Kendaraan Bermotor</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- <button onclick="openModalPerpanjangSemua()"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                    <i class="fa fa-rotate text-sm"></i>
                    Perpanjang Semua
                </button> -->
                <button onclick="openModalTambah()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                    <i class="fa fa-plus text-sm"></i>
                    Tambah Data
                </button>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

            {{-- Total KIR --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Jumlah KIR</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $jumlahKir }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Biaya KIR --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Biaya KIR</p>
                        <h3 class="text-lg font-bold text-indigo-600 mt-2 leading-tight">
                            Rp {{ number_format($totalBiayaKir, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- KIR Aktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">KIR Aktif</p>
                        <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $jumlahAktif }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Segera Berakhir --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Segera Berakhir</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $jumlahSegera }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- KIR Nonaktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">KIR Nonaktif</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $jumlahNonaktif }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar KIR Kendaraan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} total data KIR</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">

                    {{-- SERVER-SIDE FILTER FORM --}}
                    <form method="GET" action="{{ request()->url() }}" id="filterFormKir" class="flex flex-wrap items-center gap-2">

                        {{-- FILTER STATUS --}}
                        <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-0.5 bg-gray-50">
                            @foreach([''=>'Semua','aktif'=>'Aktif','nonaktif'=>'Nonaktif'] as $val => $label)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => 1]) }}"
                                   class="px-3 py-1 text-xs font-medium rounded-md transition-colors
                                       {{ request('status', '') === $val
                                           ? ($val === 'aktif' ? 'bg-white text-green-700 shadow-sm border border-gray-200' : ($val === 'nonaktif' ? 'bg-white text-red-600 shadow-sm border border-gray-200' : 'bg-white text-gray-700 shadow-sm border border-gray-200'))
                                           : 'text-gray-500 hover:text-gray-700' }}">
                                    @if($val === 'aktif') <i class="fa fa-check text-[10px]"></i> @endif
                                    @if($val === 'nonaktif') <i class="fa fa-times text-[10px]"></i> @endif
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>

                        {{-- FILTER TAHUN --}}
                        <select name="tahun" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                            <option value="">Semua Tahun</option>
                            @foreach(range(date('Y') + 1, date('Y') - 3) as $yr)
                                <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>

                        {{-- FILTER HARI --}}
                        <select name="hari" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                            <option value="">Semua Hari</option>
                            @for ($d = 1; $d <= 31; $d++)
                                <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}" {{ request('hari') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>

                        {{-- FILTER BULAN --}}
                        <div class="flex items-center gap-1">
                            <input type="month" name="bulan" value="{{ request('bulan') }}"
                                onchange="this.form.submit()"
                                class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            @if(request('bulan'))
                                <a href="{{ request()->fullUrlWithQuery(['bulan' => null, 'page' => 1]) }}"
                                   class="px-2 py-1.5 text-xs text-gray-400 hover:text-red-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endif
                        </div>

                        {{-- Preserve status in form if using link-based status filter --}}
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                        {{-- SEARCH INPUT --}}
                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" placeholder="Cari nopol, no uji..." value="{{ request('search') }}"
                                class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                        </div>

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa fa-search text-xs"></i> Cari
                        </button>

                        @if(request()->hasAny(['search','status','tahun','bulan','hari']))
                            <a href="{{ request()->url() }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fa fa-rotate-left text-xs"></i> Reset
                            </a>
                        @endif

                    </form>

                    <a href="{{ route('kir.pdf', request()->only(['search','status','tahun','bulan','hari'])) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>

                    <a href="{{ request()->fullUrlWithQuery([]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </a>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kendaraan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                                Uji</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Tanggal Bayar</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jatuh
                                Tempo</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Lampiran</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($data as $d)
                            @php
                                $today = now()->startOfDay();
                                $masa = \Carbon\Carbon::parse($d->masa_berlaku)->startOfDay();
                                $selisih = (int) $today->diffInDays($masa, false);
                                $rowStatus = $selisih < 0 ? 'nonaktif' : 'aktif';
                            @endphp
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400 row-number">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-gray-800">{{ $d->kendaraan->nopol ?? '-' }}</span>
                                    @if ($d->kendaraan->merk ?? false)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $d->kendaraan->merk }}</p>
                                    @endif
                                </td>

                                {{-- No Uji --}}
                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_uji }}</span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $d->tanggal_bayar ? \Carbon\Carbon::parse($d->tanggal_bayar)->format('d M Y') : '-' }}
                                </td>

                                {{-- Masa Berlaku --}}
                                <td class="px-4 py-3.5">
                                    @php
                                        $today    = now()->startOfDay();
                                        $masa     = \Carbon\Carbon::parse($d->masa_berlaku)->startOfDay();
                                        $selisih  = (int) $today->diffInDays($masa, false);
                                        $sisaDetikKir = (int) (\Carbon\Carbon::parse($d->masa_berlaku)->endOfDay()->timestamp - now()->timestamp);
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $masa->format('d M Y') }}</span>
                                        @if ($selisih < 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                Terlambat {{ formatSisaWaktu(abs($sisaDetikKir)) }}
                                            </span>
                                        @elseif ($selisih <= $reminder)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold w-fit animate-pulse
                                                {{ $selisih == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                <span class="w-2 h-2 rounded-full {{ $selisih == 0 ? 'bg-red-500' : 'bg-yellow-500' }}"></span>
                                                @if ($selisih == 0)
                                                    Berakhir {{ formatSisaWaktu($sisaDetikKir) }} lagi
                                                @elseif ($selisih == 1)
                                                    Berakhir Besok
                                                @else
                                                    Berakhir dalam {{ $selisih }} hari
                                                @endif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold w-fit">
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                Aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    Rp {{ number_format($d->biaya, 0, ',', '.') }}
                                </td>

                                {{-- Bukti --}}
                                <td class="px-4 py-3.5">
                                    @if ($d->image)
                                        @php $filename = basename($d->image); @endphp
                                        <a href="{{ asset($d->image) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800 block">
                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Lampiran --}}
                                <td class="px-4 py-3.5">
                                    @if($d->attachments->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach ($d->attachments as $att)
                                                <div class="flex items-center gap-1">
                                                    <a href="{{ asset($att->file_path) }}" target="_blank"
                                                        class="text-blue-500 underline text-[11px] hover:text-blue-700">
                                                        {{ $att->file_name }}
                                                    </a>
                                                    <form action="{{ route('kir.attachment.destroy', $att->id) }}" method="POST"
                                                        onsubmit="return confirm('Hapus lampiran ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-400 hover:text-red-600 text-[10px]">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Perpanjang: hanya tampil jika sudah dalam batas reminder --}}
                                        @if ($selisih <= $reminder)
                                        <button
                                            onclick="openModalPerpanjang(
        '{{ $d->id }}',
        '{{ $d->kendaraan->nopol ?? '-' }}',
        '{{ $d->kendaraan->merk ?? '-' }}',
        '{{ $d->no_uji }}',
        '{{ $d->biaya }}',
        '{{ \Carbon\Carbon::parse($d->masa_berlaku)->format('Y-m-d') }}',
        '{{ $d->tanggal_bayar ? \Carbon\Carbon::parse($d->tanggal_bayar)->format('Y-m-d') : '' }}'
    )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fa fa-rotate-right text-xs"></i> Perpanjang
                                        </button>
                                        @endif

                                        <!-- <button
                                            onclick="openEditModal(
        '{{ $d->id }}',
        '{{ $d->kendaraan_id }}',
        '{{ $d->no_uji }}',
        '{{ $d->biaya }}',
        '{{ $d->masa_berlaku }}',
        '{{ $d->image ? asset($d->image) : '' }}',
        '{{ $d->image ? basename($d->image) : '' }}'
    )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button> -->


                                        <form action="/admin/kir/{{ $d->id }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="fa fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa-solid fa-file-circle-check text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data KIR</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER: SHOWING INFO + PAGINATION --}}
            <div class="border-t border-gray-100 bg-gray-50/50">
                <x-pagination :paginator="$data" />
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Data KIR</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data uji kendaraan bermotor</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="/admin/kir" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf
                <input type="hidden" name="_modal_open" value="tambah">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                            class="text-red-500">*</span></label>
                    <select name="kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nopol }} - {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Uji <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" required placeholder="Nomor uji kendaraan"
                        value="{{ old('no_uji') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" id="tambah_tanggal_bayar" required
                        value="{{ old('tanggal_bayar') }}"
                        oninput="hitungMasaBerlaku()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jatuh Tempo</label>
                    <input type="date" id="tambah_masa_berlaku_display" disabled
                        value="{{ old('masa_berlaku') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 cursor-not-allowed">
                    <input type="hidden" name="masa_berlaku" id="tambah_masa_berlaku" value="{{ old('masa_berlaku') }}">
                    <p class="text-xs text-gray-400 mt-1">Otomatis tanggal bayar + 1 tahun</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya <span
                            class="text-red-500">*</span></label>

                    <input type="number" min="0" max="9999999999" name="biaya"
                        placeholder="Tambahkan biaya kir"
                        value="{{ old('biaya') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        required>
                </div>



                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Upload File <span class="text-red-500">*</span>
                    </label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrap" class="hidden mb-3 relative"></div>

                    {{-- UPLOAD AREA --}}
                    <label for="image"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>

                        <span class="text-xs text-slate-500">
                            Klik untuk upload / ganti file
                        </span>

                        <span class="text-[11px] text-slate-400">
                            (maks 2MB)
                        </span>
                    </label>

                    <input type="file" name="image" id="image" class="hidden" onchange="previewFile(this)"
                        required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>
                    <input type="file" name="bukti_attachment[]" multiple
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                        onchange="renderListAttachment(this, 'listAttachmentPerpanjang')">
                    <ul id="listAttachmentPerpanjang" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================
    MODAL EDIT
====================================== --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Data KIR</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data uji kendaraan bermotor</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data" class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                            class="text-red-500">*</span></label>
                    <select name="kendaraan_id" id="edit_kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->nopol }} - {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Uji <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" id="edit_no_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="masa_berlaku" id="edit_masa_berlaku" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <p class="text-xs text-gray-400 mt-1">Isi tanggal masa berlaku KIR</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya <span
                            class="text-red-500">*</span></label>
                    <input type="number" min="0" name="biaya" id="edit_biaya" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Image</label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrapEdit" class="hidden mb-3 relative"></div>

                    {{-- UPLOAD AREA --}}
                    <label for="edit_image"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>

                        <span class="text-xs text-slate-500">
                            Klik untuk upload / ganti file
                        </span>

                        <span class="text-[11px] text-slate-400">
                            (maks 2MB)
                        </span>
                    </label>

                    <input type="file" name="image" id="edit_image" class="hidden"
                        onchange="previewFileEdit(this)">


                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Update
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- MODAL PERPANJANG --}}
    <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Perpanjang KIR Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Data lama akan disimpan ke history, lalu diperbarui.</p>
                </div>
                <button onclick="closeModalPerpanjang()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf
                <input type="hidden" name="_modal_open" value="perpanjang">
                {{-- Hidden fields konteks perpanjang (untuk reopen saat validasi gagal) --}}
                <input type="hidden" name="_perpanjang_id"            id="_perpanjang_id"            value="{{ old('_perpanjang_id') }}">
                <input type="hidden" name="_perpanjang_nopol"         id="_perpanjang_nopol"         value="{{ old('_perpanjang_nopol') }}">
                <input type="hidden" name="_perpanjang_merk"          id="_perpanjang_merk"          value="{{ old('_perpanjang_merk') }}">
                <input type="hidden" name="_perpanjang_no_uji"        id="_perpanjang_no_uji"        value="{{ old('_perpanjang_no_uji') }}">
                <input type="hidden" name="_perpanjang_biaya"         id="_perpanjang_biaya"         value="{{ old('_perpanjang_biaya') }}">
                <input type="hidden" name="_perpanjang_masa_berlaku_lama" id="_perpanjang_masa_berlaku_lama" value="{{ old('_perpanjang_masa_berlaku_lama') }}">

                {{-- Kendaraan readonly --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kendaraan</label>
                    <div class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed">
                        <span id="perpanjang_kendaraan_text">-</span>
                    </div>
                </div>

                {{-- No Uji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">No Uji Baru <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" id="perpanjang_no_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Masa Berlaku Baru: lama + 1 tahun, disabled --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Masa Berlaku Baru <span
                            class="text-red-500">*</span></label>
                    <input type="date" id="perpanjang_masa_berlaku_display" disabled
                        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                    <input type="hidden" name="masa_berlaku" id="perpanjang_masa_berlaku">
                    <p class="text-xs text-gray-400 mt-1">Otomatis masa berlaku lama + 1 tahun</p>
                </div>

                {{-- Tanggal Bayar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="perpanjang_tanggal_bayar"
                        value="{{ old('tanggal_bayar') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Biaya Baru <span
                            class="text-red-500">*</span></label>
                    <input type="number" min="0" name="biaya" id="perpanjang_biaya" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Upload Bukti --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Upload Bukti Baru <span class="text-red-500">*</span>
                    </label>

                    {{-- Preview area --}}
                    <div id="previewWrapPerpanjang" class="hidden mb-3 relative"></div>

                    <label for="perpanjang_image"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>
                        <span class="text-xs text-slate-500">Klik untuk upload / ganti file</span>
                        <span class="text-[11px] text-slate-400">(maks 2MB)</span>
                    </label>

                    <input type="file" name="image" id="perpanjang_image" class="hidden" required
                        onchange="previewFilePerpanjang(this)">
                </div>

                {{-- Lampiran Tambahan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran Tambahan <span class="text-gray-400 font-normal">(opsional, bisa lebih dari 1)</span>
                    </label>

                    <label for="bukti_attachment"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan</span>
                        <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                    </label>

                    <input type="file" name="bukti_attachment[]" id="bukti_attachment" class="hidden" multiple
                        onchange="renderListAttachment(this, 'listAttachmentTambah')">

                    <ul id="listAttachmentTambah" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalPerpanjang()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btnPerpanjangKir"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa fa-rotate-right"></i> Perpanjang KIR
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================
    POPUP ALERT
====================================== --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">

                @if (session('success'))
                    <div
                        class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif


    {{-- STYLE & SCRIPT --}}
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        // Fungsi hitung masa berlaku otomatis dari tanggal bayar + 1 tahun
        function hitungMasaBerlaku() {
            const tglBayar = document.getElementById('tambah_tanggal_bayar').value;
            if (!tglBayar) return;
            
            const d = new Date(tglBayar);
            d.setFullYear(d.getFullYear() + 1);
            const val = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            
            document.getElementById('tambah_masa_berlaku_display').value = val;
            document.getElementById('tambah_masa_berlaku').value = val;
        }

        // -- AUTO-REOPEN MODAL PADA VALIDATION ERROR ---
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            @if (old('_modal_open') === 'perpanjang')
                // Reopen modal perpanjang dengan data old()
                (function() {
                    var id               = '{{ old('_perpanjang_id') }}';
                    var nopol            = '{{ old('_perpanjang_nopol') }}';
                    var merk             = '{{ old('_perpanjang_merk') }}';
                    var no_uji           = '{{ old('no_uji') }}';
                    var biaya            = '{{ old('biaya') }}';
                    var masaBerlakuLama  = '{{ old('_perpanjang_masa_berlaku_lama') }}';

                    if (typeof openModalPerpanjang === 'function') {
                        openModalPerpanjang(id, nopol, merk, no_uji, biaya, masaBerlakuLama);
                    }
                })();
            @else
                openModalTambah();
                // Re-trigger hitungMasaBerlaku jika tanggal_bayar sudah terisi (old())
                if (document.getElementById('tambah_tanggal_bayar').value) {
                    hitungMasaBerlaku();
                }
            @endif
        });
        @endif

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function openModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // -- MODAL EDIT -------------------------------------
        function openEditModal(id, kendaraan_id, no_uji, biaya, masa_berlaku, image, imageName) {
            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = '/admin/kir/' + id;
            document.getElementById('edit_kendaraan_id').value = kendaraan_id;
            document.getElementById('edit_no_uji').value = no_uji;
            document.getElementById('edit_biaya').value = biaya;

            // Set masa berlaku dari data yang ada
            document.getElementById('edit_masa_berlaku').value = masa_berlaku;

            document.getElementById('edit_image').value = '';

            const wrap = document.getElementById('previewWrapEdit');
            wrap.innerHTML = '';

            if (image) {
                wrap.classList.remove('hidden');

                const ext = image.split('.').pop().toLowerCase();
                let html = '';

                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    html = `
                <div class="relative">
                    <img src="${image}" class="h-36 w-full object-cover rounded-xl border">
                    <span class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[11px] px-2 py-1 truncate">
                        ${imageName}
                    </span>
                    <a href="${image}" target="_blank"
                        class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center bg-white/90 text-gray-600 rounded-full text-xs">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            `;
                } else {
                    let icon = 'fa-file';
                    let color = 'text-gray-600 bg-gray-50';

                    if (ext === 'pdf') {
                        icon = 'fa-file-pdf';
                        color = 'text-red-600 bg-red-50';
                    }
                    if (ext === 'doc' || ext === 'docx') {
                        icon = 'fa-file-word';
                        color = 'text-blue-600 bg-blue-50';
                    }

                    html = `
                <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                    <div class="flex items-center gap-2 text-sm font-semibold truncate">
                        <i class="fa-solid ${icon}"></i>
                        <span class="truncate">${imageName}</span>
                    </div>
                    <a href="${image}" target="_blank" class="text-xs underline flex-shrink-0 ml-2">Lihat</a>
                </div>
            `;
                }

                wrap.innerHTML = html;
            } else {
                wrap.classList.add('hidden');
            }
        }
        

        function closeModalEdit() {
            var m = document.getElementById('modalEdit');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalEdit').addEventListener('click', function(e) {
            if (e.target === this) closeModalEdit();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalTambah();
                closeModalEdit();
            }
        });

        function syncPerpanjangKirDates() {
            // Fungsi ini sekarang tidak perlu melakukan apa-apa untuk masa_berlaku
            // karena masa_berlaku sudah dihitung dari masaBerlakuLama saat modal dibuka
        }

        const tanggalBayarKirInput = document.getElementById('perpanjang_tanggal_bayar');
        if (tanggalBayarKirInput) {
            tanggalBayarKirInput.addEventListener('change', syncPerpanjangKirDates);
        }

        // -- MODAL PERPANJANG -------------------------------
        function openModalPerpanjang(id, nopol, merk, no_uji, biaya, masaBerlakuLama, tanggalBayarLama) {
            document.getElementById('formPerpanjang').action = '/admin/kir/' + id + '/perpanjang';
            document.getElementById('perpanjang_kendaraan_text').innerText = nopol + ' - ' + merk;
            document.getElementById('perpanjang_no_uji').value = no_uji;
            document.getElementById('perpanjang_biaya').value = biaya;

            // Simpan konteks ke hidden fields (untuk reopen saat validasi gagal)
            document.getElementById('_perpanjang_id').value                   = id;
            document.getElementById('_perpanjang_nopol').value                = nopol;
            document.getElementById('_perpanjang_merk').value                 = merk;
            document.getElementById('_perpanjang_no_uji').value               = no_uji;
            document.getElementById('_perpanjang_biaya').value                = biaya;
            document.getElementById('_perpanjang_masa_berlaku_lama').value    = masaBerlakuLama;

            // tanggal_bayar: default = tanggal_bayar_lama + 1 tahun, min = nilai tersebut
            var tglBayarEl = document.getElementById('perpanjang_tanggal_bayar');
            var today = new Date().toISOString().split('T')[0];
            if (tanggalBayarLama) {
                var dk = new Date(tanggalBayarLama);
                dk.setFullYear(dk.getFullYear() + 1);
                var defaultTglKir = dk.getFullYear() + '-'
                    + String(dk.getMonth() + 1).padStart(2, '0') + '-'
                    + String(dk.getDate()).padStart(2, '0');
                tglBayarEl.value = defaultTglKir;
                tglBayarEl.min   = defaultTglKir;
            } else {
                tglBayarEl.value = today;
                tglBayarEl.min   = today;
            }

            // masa_berlaku_baru = masaBerlakuLama + 1 tahun (dari data lama, tidak berubah saat tanggal_bayar berubah)
            if (masaBerlakuLama) {
                const d = new Date(masaBerlakuLama);
                d.setFullYear(d.getFullYear() + 1);
                const val = d.getFullYear() + '-'
                    + String(d.getMonth() + 1).padStart(2, '0') + '-'
                    + String(d.getDate()).padStart(2, '0');
                document.getElementById('perpanjang_masa_berlaku_display').value = val;
                document.getElementById('perpanjang_masa_berlaku').value = val;
            }

            var m = document.getElementById('modalPerpanjang');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalPerpanjang() {
            var m = document.getElementById('modalPerpanjang');
            m.classList.add('hidden');
            m.classList.remove('flex');
            // reset upload
            removePreviewPerpanjang();
            document.getElementById('listAttachmentTambah').innerHTML = '';
            document.getElementById('bukti_attachment').value = '';
        }

        document.getElementById('modalPerpanjang').addEventListener('click', function(e) {
            if (e.target === this) closeModalPerpanjang();
        });

        // -- POPUP ALERT ------------------------------------
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box = document.getElementById('alertBox');
            if (!overlay) return;

            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);

            var timer = setTimeout(closeAlert, 4500);

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeAlert();
            });

            function closeAlert() {
                clearTimeout(timer);
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                box.style.transform = 'translateY(-16px)';
            }
            window.closeAlert = closeAlert;
        })();

        function previewFile(input) {
            const wrap = document.getElementById('previewWrap');
            wrap.innerHTML = '';
            wrap.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);

            let html = '';

            // IMAGE PREVIEW
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
            <div class="relative">
                <img src="${url}"
                    class="h-36 w-full object-cover rounded-xl border">

                <button type="button"
                    onclick="removePreview()"
                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            // PDF / DOC / DOCX
            else {
                let icon = 'fa-file';
                let color = 'text-gray-600 bg-gray-50';

                if (ext === 'pdf') {
                    icon = 'fa-file-pdf';
                    color = 'text-red-600 bg-red-50';
                }

                if (ext === 'doc' || ext === 'docx') {
                    icon = 'fa-file-word';
                    color = 'text-blue-600 bg-blue-50';
                }

                html = `
            <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <i class="fa-solid ${icon}"></i>
                    ${file.name}
                </div>

                <button type="button"
                    onclick="removePreview()"
                    class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            wrap.innerHTML = html;
        }

        function removePreview() {
            const input = document.getElementById('image');
            const wrap = document.getElementById('previewWrap');

            input.value = '';
            wrap.innerHTML = '';
            wrap.classList.add('hidden');
        }

        function previewFileEdit(input) {
            const wrap = document.getElementById('previewWrapEdit');
            wrap.innerHTML = '';
            wrap.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);

            let html = '';

            // IMAGE PREVIEW
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
            <div class="relative">
                <img src="${url}"
                    class="h-36 w-full object-cover rounded-xl border">

                <button type="button"
                    onclick="removePreviewEdit()"
                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            // PDF / DOC / DOCX
            else {
                let icon = 'fa-file';
                let color = 'text-gray-600 bg-gray-50';

                if (ext === 'pdf') {
                    icon = 'fa-file-pdf';
                    color = 'text-red-600 bg-red-50';
                }

                if (ext === 'doc' || ext === 'docx') {
                    icon = 'fa-file-word';
                    color = 'text-blue-600 bg-blue-50';
                }

                html = `
            <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <i class="fa-solid ${icon}"></i>
                    ${file.name}
                </div>

                <button type="button"
                    onclick="removePreviewEdit()"
                    class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            wrap.innerHTML = html;
        }

        function removePreviewEdit() {
            const input = document.getElementById('edit_image');
            const wrap = document.getElementById('previewWrapEdit');

            input.value = '';
            wrap.innerHTML = '';
            wrap.classList.add('hidden');
        }

        function previewFilePerpanjang(input) {
            const wrap = document.getElementById('previewWrapPerpanjang');
            wrap.innerHTML = '';
            wrap.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);
            let html = '';

            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
                    <div class="relative">
                        <img src="${url}" class="h-36 w-full object-cover rounded-xl border">
                        <button type="button" onclick="removePreviewPerpanjang()"
                            class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                    </div>`;
            } else {
                let icon = 'fa-file';
                let color = 'text-gray-600 bg-gray-50';
                if (ext === 'pdf') { icon = 'fa-file-pdf'; color = 'text-red-600 bg-red-50'; }
                if (ext === 'doc' || ext === 'docx') { icon = 'fa-file-word'; color = 'text-blue-600 bg-blue-50'; }
                html = `
                    <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <i class="fa-solid ${icon}"></i> ${file.name}
                        </div>
                        <button type="button" onclick="removePreviewPerpanjang()"
                            class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                    </div>`;
            }

            wrap.innerHTML = html;
        }

        function removePreviewPerpanjang() {
            const input = document.getElementById('perpanjang_image');
            const wrap = document.getElementById('previewWrapPerpanjang');
            input.value = '';
            wrap.innerHTML = '';
            wrap.classList.add('hidden');
        }
    </script>

    {{-- ======================================
    MODAL PERPANJANG SEMUA
====================================== --}}
    <div id="modalPerpanjangSemua" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Perpanjang Semua KIR</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Semua data KIR akan diperpanjang +1 tahun dari masa berlaku masing-masing. Data lama tersimpan ke history.</p>
                </div>
                <button onclick="closeModalPerpanjangSemua()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('kir.perpanjang-semua') }}" method="POST" class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf

                {{-- Info jumlah data --}}
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-info text-blue-500 text-lg"></i>
                    <p class="text-xs text-blue-700">
                        Total <strong>{{ $data->total() }}</strong> data KIR akan diperpanjang sekaligus.
                    </p>
                </div>

                {{-- Tanggal Bayar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk menggunakan tanggal hari ini.</p>
                </div>

                {{-- Biaya Default (opsional) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Biaya Baru <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="number" min="0" max="9999999999" name="biaya_default"
                        placeholder="Kosongkan untuk pakai biaya masing-masing"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400">
                    <p class="text-xs text-gray-400 mt-1">Jika diisi, semua KIR akan diupdate dengan biaya ini.</p>
                </div>

                {{-- Konfirmasi --}}
                <div class="flex items-start gap-2 bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500 mt-0.5"></i>
                    <p class="text-xs text-yellow-700">
                        Tindakan ini tidak dapat dibatalkan. Pastikan data sudah benar sebelum melanjutkan.
                    </p>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalPerpanjangSemua()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <!-- <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa fa-rotate"></i> Ya, Perpanjang Semua
                    </button> -->
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalPerpanjangSemua() {
            var m = document.getElementById('modalPerpanjangSemua');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalPerpanjangSemua() {
            var m = document.getElementById('modalPerpanjangSemua');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        document.getElementById('modalPerpanjangSemua').addEventListener('click', function(e) {
            if (e.target === this) closeModalPerpanjangSemua();
        });

        // ── Anti double-submit: disable tombol saat form perpanjang di-submit ──
        (function () {
            var form = document.getElementById('formPerpanjang');
            var btn  = document.getElementById('btnPerpanjangKir');
            if (!form || !btn) return;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
                btn.classList.add('opacity-60', 'cursor-not-allowed');
            });
        })();

        // Fungsi render list attachment
        window.renderListAttachment = function(input, listId) {
            const list = document.getElementById(listId);
            list.innerHTML = '';

            Array.from(input.files).forEach(file => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-1.5';
                li.innerHTML = `<i class="fa-solid fa-paperclip text-slate-400"></i> ${file.name}`;
                list.appendChild(li);
            });
        };
    </script>

@endsection
