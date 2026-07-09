@extends('admin.layouts.app')

@section('title', 'Buku Besar')

@section('content')

    @php
        $totalJurnal = $data->count();
        $totalDebit = $data->sum('debit');
        $totalKredit = $data->sum('kredit');
        $totalSaldo = $data->sum('saldo');
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buku Besar</h1>
                <p class="text-sm text-gray-500 mt-0.5">Data transaksi buku besar</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- TAB HEADER --}}
            <div class="flex border-b border-gray-100 px-5 pt-4 gap-1">
                <button onclick="switchTab('buku-besar')" id="tab-buku-besar"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg border-b-2 border-blue-600 text-blue-600 transition-colors">
                    <i class="fa fa-book mr-1.5"></i> Buku Besar
                </button>
                <button onclick="switchTab('laba-rugi')" id="tab-laba-rugi"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa fa-chart-line mr-1.5"></i> Laba Rugi
                </button>
                <button onclick="switchTab('neraca')" id="tab-neraca"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa fa-scale-balanced mr-1.5"></i> Neraca
                </button>
                <button onclick="switchTab('arus-kas')" id="tab-arus-kas"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa fa-water mr-1.5"></i> Arus Kas
                </button>
            </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Total Jurnal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Jurnal</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalJurnal }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-book text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Debit --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Debit</p>
                        <h2 class="text-xl font-bold text-green-600 mt-2 leading-tight">
                            Rp {{ number_format($totalDebit, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-down text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Kredit --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Kredit</p>
                        <h2 class="text-xl font-bold text-red-600 mt-2 leading-tight">
                            Rp {{ number_format($totalKredit, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-up text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Saldo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Saldo</p>
                        <h2 class="text-xl font-bold text-yellow-600 mt-2 leading-tight">
                            Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- NAV TABS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            

            {{-- TAB: BUKU BESAR --}}
            <div id="pane-buku-besar">

                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-base">Daftar Jurnal</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $totalJurnal }} total data buku besar</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('bukubesar.pdf', ['search' => request('search')]) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                            <i class="fa fa-file-pdf text-xs"></i> PDF
                        </a>

                        <a href="{{ route('bukubesar.export.excel', ['search' => request('search')]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                            <i class="fa fa-file-excel text-xs"></i> Excel
                        </a>

                        <a href="{{ route('bukubesar.export.csv', ['search' => request('search')]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700 transition">
                            <i class="fa fa-file-csv text-xs"></i> CSV
                        </a>
                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <form method="GET" action="{{ route('bukubesar.index') }}" class="relative">
                                <i
                                    class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>

                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari kode, transaksi, kategori..."
                                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg w-56">

                                <button type="submit" class="hidden">Search</button>
                            </form>
                        </div>
                        <button onclick="window.location.reload()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fa fa-sync text-xs"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    No
                                </th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Tanggal</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Kode
                                    Jurnal</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Transaksi</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Kategori</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Debit</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Kredit</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Saldo</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Aktivitas</th>
                                <th
                                    class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse ($data as $item)
                                <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                    data-search="{{ strtolower($item->kode_jurnal . ' ' . $item->transaksi . ' ' . $item->kategori . ' ' . $item->aktivitas . ' ' . $item->referensi) }}">

                                    <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $data->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->tanggal }}</td>

                                    <td class="px-4 py-3.5">
                                        <span
                                            class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->kode_jurnal }}</span>
                                        @if ($item->referensi)
                                            <br>
                                            <span class="inline-flex items-center gap-1 mt-1 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded-full">
                                                <i class="fa fa-link text-[10px]"></i>{{ $item->referensi }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 text-sm text-gray-700 max-w-[160px] truncate">
                                        {{ $item->transaksi }}
                                    </td>

                                    <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->kategori }}</td>


                                    <td class="px-4 py-3.5">
                                        <span class="text-sm font-medium text-green-600">Rp
                                            {{ number_format($item->debit, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <span class="text-sm font-medium text-red-600">Rp
                                            {{ number_format($item->kredit, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <span class="text-sm font-bold text-yellow-600">Rp
                                            {{ number_format($item->saldo, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->aktivitas }}</td>

                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button
                                                onclick="openEditModal(
                                            '{{ $item->id }}',
                                            '{{ $item->kode_jurnal }}',
                                            '{{ addslashes($item->transaksi) }}',
                                            '{{ addslashes($item->kategori) }}',
                                            '{{ $item->tanggal }}',
                                            '{{ $item->debit }}',
                                            '{{ $item->kredit }}',
                                            '{{ $item->saldo }}',
                                            '{{ addslashes($item->aktivitas) }}',
                                            `{{ $item->keterangan }}`
                                        )"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                                <i class="fa fa-edit text-xs"></i> Edit
                                            </button>
                                            <form action="{{ route('bukubesar.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                                class="inline">
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
                                    <td colspan="10" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fa-solid fa-book text-2xl text-gray-300"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500">Belum ada data buku besar</p>
                                            <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
                </div>

            </div>{{-- end pane-buku-besar --}}

            {{-- TAB: LABA RUGI --}}
            <div id="pane-laba-rugi" class="hidden">

                @php
                    $pendapatan = $data->where('kategori', 'Pendapatan')->sum('kredit');
                    $bebanPokok = $data
                        ->filter(
                            fn($i) => $i->kategori == 'Beban' &&
                                str_contains(strtolower($i->transaksi . ' ' . $i->keterangan), 'pokok'),
                        )
                        ->sum('debit');
                    $totalBeban = $data->where('kategori', 'Beban')->sum('debit');
                    $labaKotor = $pendapatan - $bebanPokok;
                    $labaBersih = $pendapatan - $totalBeban;
                @endphp

                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-base">Laba Rugi</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Kalkulasi dari data buku besar</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('bukubesar.laba-rugi.pdf') }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                            <i class="fa fa-file-pdf text-xs"></i> PDF
                        </a>
                        <a href="{{ route('bukubesar.laba-rugi.excel') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                            <i class="fa fa-file-excel text-xs"></i> Excel
                        </a>
                        <a href="{{ route('bukubesar.laba-rugi.csv') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700 transition">
                            <i class="fa fa-file-csv text-xs"></i> CSV
                        </a>
                    </div>
                </div>

                {{-- SUMMARY LABA RUGI --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-5 py-4">

                    <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                        <p class="text-xs text-red-500 font-semibold mb-1">Total Beban</p>
                        <p class="text-base font-bold text-red-700">Rp {{ number_format($totalBeban, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <p class="text-xs text-green-500 font-semibold mb-1">Total Pendapatan</p>
                        <p class="text-base font-bold text-green-700">Rp {{ number_format($pendapatan, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <p class="text-xs text-blue-500 font-semibold mb-1">Laba Kotor</p>
                        <p class="text-base font-bold {{ $labaKotor < 0 ? 'text-red-700' : 'text-blue-700' }}">
                            Rp {{ number_format($labaKotor, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                        <p class="text-xs text-indigo-500 font-semibold mb-1">Laba Bersih</p>
                        <p class="text-base font-bold {{ $labaBersih < 0 ? 'text-red-700' : 'text-indigo-700' }}">
                            Rp {{ number_format($labaBersih, 0, ',', '.') }}
                        </p>
                    </div>

                </div>

                {{-- TABEL LABA RUGI --}}
                <div class="overflow-x-auto px-5 pb-5">
                    <table class="w-full text-sm border border-gray-100 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th
                                    class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-12">
                                    No</th>
                                <th
                                    class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Total Beban</th>
                                <th
                                    class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Total Pendapatan</th>
                                <th
                                    class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Laba Kotor</th>
                                <th
                                    class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Laba Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-gray-50 {{ $labaBersih < 0 ? 'bg-red-50' : 'bg-green-50' }}">
                                <td class="px-4 py-3.5 text-center text-xs text-gray-400 font-medium">1</td>
                                <td class="px-4 py-3.5 text-right text-sm font-semibold text-red-600">
                                    Rp {{ number_format($totalBeban, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right text-sm font-semibold text-green-600">
                                    Rp {{ number_format($pendapatan, 2, ',', '.') }}
                                </td>
                                <td
                                    class="px-4 py-3.5 text-right text-sm font-semibold {{ $labaKotor < 0 ? 'text-red-600' : 'text-blue-600' }}">
                                    Rp {{ number_format($labaKotor, 2, ',', '.') }}
                                </td>
                                <td
                                    class="px-4 py-3.5 text-right text-sm font-bold {{ $labaBersih < 0 ? 'text-red-600' : 'text-indigo-600' }}">
                                    Rp {{ number_format($labaBersih, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>{{-- end pane-laba-rugi --}}

            {{-- TAB: NERACA --}}
            <div id="pane-neraca" class="hidden">

                @php
                    $getSaldo = fn($kategori, $like = null) => $data
                        ->filter(
                            fn($i) => $i->kategori == $kategori &&
                                ($like ? stripos($i->transaksi, $like) !== false : true),
                        )
                        ->sum('saldo');

                    $asetLancar =
                        $getSaldo('Aktiva', 'kas') +
                        $getSaldo('Aktiva', 'piutang') +
                        $getSaldo('Aktiva', 'persediaan') +
                        $getSaldo('Aktiva', 'uang muka');
                    $asetTetap = $getSaldo('Aktiva', 'peralatan') - $getSaldo('Aktiva', 'penyusutan');
                    $totalAset = $asetLancar + $asetTetap;
                    $kewajibanPendek = $getSaldo('Kewajiban', 'hutang usaha') + $getSaldo('Kewajiban', 'gaji');
                    $kewajibanPanjang = $getSaldo('Kewajiban', 'hutang bank');
                    $totalKewajiban = $kewajibanPendek + $kewajibanPanjang;
                    $modal = $getSaldo('Modal');
                    $totalEkuitas = $modal + $labaBersih;
                    $totalPasiva = $totalKewajiban + $totalEkuitas;
                @endphp

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-base">Neraca</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Posisi keuangan berdasarkan data buku besar</p>
                    </div>
                </div>

                <div class="overflow-x-auto px-5 py-5">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th
                                    class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-16">
                                    No</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Keterangan</th>
                                <th
                                    class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-48">
                                    Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- ASET --}}
                            <tr class="bg-green-600">
                                <td colspan="3" class="px-4 py-2.5 text-xs font-bold text-white tracking-widest">ASET
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">01</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Kas & Bank</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">Rp
                                    {{ number_format($getSaldo('Aktiva', 'kas'), 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">02</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Piutang Usaha</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">Rp
                                    {{ number_format($getSaldo('Aktiva', 'piutang'), 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700">Total Aset Lancar</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Rp
                                    {{ number_format($asetLancar, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700">Total Aset Tetap</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Rp
                                    {{ number_format($asetTetap, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-green-50 border-b-2 border-green-200">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-sm font-bold text-green-800">TOTAL ASET</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-green-800">Rp
                                    {{ number_format($totalAset, 0, ',', '.') }}</td>
                            </tr>

                            {{-- KEWAJIBAN --}}
                            <tr class="bg-red-600">
                                <td colspan="3" class="px-4 py-2.5 text-xs font-bold text-white tracking-widest">
                                    KEWAJIBAN</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">03</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Kewajiban Jangka Pendek</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">Rp
                                    {{ number_format($kewajibanPendek, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">04</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Kewajiban Jangka Panjang</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">Rp
                                    {{ number_format($kewajibanPanjang, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-red-50 border-b-2 border-red-200">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-sm font-bold text-red-800">TOTAL KEWAJIBAN</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-red-800">Rp
                                    {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                            </tr>

                            {{-- EKUITAS --}}
                            <tr class="bg-blue-600">
                                <td colspan="3" class="px-4 py-2.5 text-xs font-bold text-white tracking-widest">
                                    EKUITAS</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">05</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Modal Disetor</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">Rp
                                    {{ number_format($modal, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">06</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Laba Bersih / Ditahan</td>
                                <td
                                    class="px-4 py-3 text-right text-sm {{ $labaBersih < 0 ? 'text-red-600' : 'text-gray-700' }}">
                                    Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-blue-50 border-b-2 border-blue-200">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-sm font-bold text-blue-800">TOTAL EKUITAS</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-blue-800">Rp
                                    {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                            </tr>

                            {{-- TOTAL KEWAJIBAN + EKUITAS --}}
                            <tr class="bg-gray-800">
                                <td class="px-4 py-3.5"></td>
                                <td class="px-4 py-3.5 text-sm font-bold text-white">TOTAL KEWAJIBAN + EKUITAS</td>
                                <td class="px-4 py-3.5 text-right text-sm font-bold text-white">Rp
                                    {{ number_format($totalPasiva, 0, ',', '.') }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>{{-- end pane-neraca --}}

            {{-- TAB: ARUS KAS --}}
            <div id="pane-arus-kas" class="hidden">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-base">Arus Kas</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Data transaksi berdasarkan aktivitas</p>
                    </div>
                </div>

                <div class="overflow-x-auto px-5 py-5">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th
                                    class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-12">
                                    No</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-32">
                                    Aktivitas</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Keterangan</th>
                                <th
                                    class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-48">
                                    Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $totalArusKas = 0;
                                $arusKasData = $data->whereNotNull('aktivitas');
                            @endphp

                            @forelse($arusKasData as $item)
                                @php
                                    $jumlah = $item->kategori == 'Pendapatan' ? $item->kredit : -$item->debit;
                                    $totalArusKas += $jumlah;
                                @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center text-xs text-gray-400">{{ $no++ }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $aktivitas = strtolower($item->aktivitas);
                                            $badgeClass = match (true) {
                                                str_contains($aktivitas, 'operasi') => 'bg-blue-100 text-blue-700',
                                                str_contains($aktivitas, 'investasi')
                                                    => 'bg-purple-100 text-purple-700',
                                                str_contains($aktivitas, 'pendanaan')
                                                    => 'bg-orange-100 text-orange-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                            {{ ucfirst($item->aktivitas) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $item->transaksi }}</td>
                                    <td
                                        class="px-4 py-3 text-right text-sm font-semibold {{ $jumlah < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($jumlah, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-400">
                                        Belum ada data arus kas. Isi kolom <span class="font-medium">Aktivitas</span> pada
                                        entri buku besar.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="bg-gray-700">
                                <td colspan="3" class="px-4 py-3.5 text-sm font-bold text-white">TOTAL ARUS KAS</td>
                                <td
                                    class="px-4 py-3.5 text-right text-sm font-bold {{ $totalArusKas < 0 ? 'text-red-300' : 'text-green-300' }}">
                                    Rp {{ number_format($totalArusKas, 2, ',', '.') }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>{{-- end pane-arus-kas --}}

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Buku Besar</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data jurnal buku besar</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('bukubesar.store') }}" method="POST"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Jurnal <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="kode_jurnal" required placeholder="Contoh: JU-001"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Transaksi <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="transaksi" required placeholder="Nama transaksi"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="kategori" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Pendapatan">Pendapatan</option>
                        <option value="Beban">Beban</option>
                        <option value="Aktiva">Aktiva</option>
                        <option value="Modal">Modal</option>
                        <option value="Kewajiban">Kewajiban</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Debit <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="debit" value="0" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kredit <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="kredit" value="0" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Saldo <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="saldo" value="0" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aktivitas <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="aktivitas" required placeholder="Jenis aktivitas"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span
                            class="text-red-500">*</span></label>
                    <textarea name="keterangan" rows="3" required placeholder="Keterangan tambahan..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Buku Besar</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data jurnal buku besar</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Jurnal <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="kode_jurnal" id="edit_kode_jurnal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Transaksi <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="transaksi" id="edit_transaksi" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="kategori" id="edit_kategori" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Pendapatan">Pendapatan</option>
                        <option value="Beban">Beban</option>
                        <option value="Aktiva">Aktiva</option>
                        <option value="Modal">Modal</option>
                        <option value="Kewajiban">Kewajiban</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="edit_tanggal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Debit <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="debit" id="edit_debit" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kredit <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="kredit" id="edit_kredit" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Saldo <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="saldo" id="edit_saldo" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aktivitas <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="aktivitas" id="edit_aktivitas" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span
                            class="text-red-500">*</span></label>
                    <textarea name="keterangan" id="edit_keterangan" rows="3" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
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


    {{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
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
        // ── MODAL TAMBAH ───────────────────────────────────
        function openModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // ── MODAL EDIT ─────────────────────────────────────
        function openEditModal(id, kode_jurnal, transaksi, kategori, tanggal, debit, kredit, saldo, aktivitas, keterangan) {
            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = '/admin/bukubesar/' + id;
            document.getElementById('edit_kode_jurnal').value = kode_jurnal;
            document.getElementById('edit_transaksi').value = transaksi;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_debit').value = debit;
            document.getElementById('edit_kredit').value = kredit;
            document.getElementById('edit_saldo').value = saldo;
            document.getElementById('edit_aktivitas').value = aktivitas;
            document.getElementById('edit_keterangan').value = keterangan;
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

        // ── SEARCH / FILTER ────────────────────────────────
        // ── TAB SWITCHER ───────────────────────────────────
        function switchTab(name) {
            const panes = ['buku-besar', 'laba-rugi', 'neraca', 'arus-kas'];
            panes.forEach(function(p) {
                document.getElementById('pane-' + p).classList.toggle('hidden', p !== name);
                const btn = document.getElementById('tab-' + p);
                if (p === name) {
                    btn.classList.add('border-blue-600', 'text-blue-600');
                    btn.classList.remove('border-transparent', 'text-gray-500');
                } else {
                    btn.classList.remove('border-blue-600', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
            });
        }

        function filterTable(q) {
            var num = 0;
            document.querySelectorAll('#tableBody tr[data-search]').forEach(function(row) {
                var show = row.dataset.search.includes(q.toLowerCase());
                row.style.display = show ? '' : 'none';
                if (show) {
                    num++;
                    row.querySelector('td:first-child').textContent = num;
                }
            });
        }

        // ── POPUP ALERT ────────────────────────────────────
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
    </script>

@endsection
