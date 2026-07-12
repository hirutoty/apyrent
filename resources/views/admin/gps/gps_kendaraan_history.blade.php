@extends('admin.layouts.app')

@section('title', 'History GPS Kendaraan')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">History Perpanjangan GPS Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat seluruh data GPS kendaraan yang telah diperpanjang.</p>
        </div>
        <a href="{{ route('gps-kendaraan-history.export', request()->query()) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
            <i class="fa fa-file-pdf"></i> Export PDF
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter + Search Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4">
        <form method="GET" action="{{ route('gps-kendaraan-history.index') }}"
              class="flex flex-wrap items-end gap-3">

            {{-- Bulan --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bulan</label>
                <select name="bulan"
                        class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm py-2 pl-3 pr-8">
                    <option value="">Semua Bulan</option>
                    @foreach([
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ] as $num => $nama)
                        <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tahun</label>
                <select name="tahun"
                        class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm py-2 pl-3 pr-8">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    <i class="fa fa-filter"></i> Filter
                </button>
                @if(request('bulan') || request('tahun'))
                    <a href="{{ route('gps-kendaraan-history.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium transition">
                        <i class="fa fa-times"></i> Reset
                    </a>
                @endif
            </div>

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- Search --}}
            <div class="flex flex-col gap-1 w-72">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cari</label>
                <input type="text" id="search"
                       placeholder="Kendaraan, GPS, type..."
                       class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Data</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">perpanjangan tercatat</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Biaya</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
                @if(request('bulan') || request('tahun'))
                    periode yang dipilih
                @else
                    semua periode
                @endif
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-10">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kendaraan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nopol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">GPS</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Biaya Sewa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tgl Habis Lama</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Diperpanjang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Bukti</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Lampiran</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Aksi</th>
                    </tr>
                </thead>

                <tbody id="tableBody" class="divide-y divide-gray-100">
                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $data->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->kendaraan->merk ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono text-xs tracking-wider">
                                    {{ $item->kendaraan->nopol ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $item->gps->nama_gps ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                                    {{ $item->type }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-gray-800 tabular-nums">
                                Rp {{ number_format($item->biaya_sewa, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ \Carbon\Carbon::parse($item->tanggal_habis)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                <div class="text-gray-800 font-medium">
                                    {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('H:i') }}
                                </div>
                            </td>

                            <td>
                                    @if ($item->bukti_bayar)
                                        @php
                                            $filename = basename($item->bukti_bayar);
                                        @endphp

                                        <a href="{{ asset($item->bukti_bayar) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                            <td class="px-4 py-3 text-center">
                                @if($item->attachments->isNotEmpty())
                                    <div class="flex flex-col gap-1">
                                        @foreach($item->attachments as $att)
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                                class="text-blue-600 underline text-xs hover:text-blue-800">
                                                *{{ $att->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('history.gpskendaraan.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus history GPS ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                   text-xs font-medium bg-red-50 text-red-600
                                                   hover:bg-red-100 transition">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <i class="fa fa-inbox text-4xl"></i>
                                    <p class="text-sm font-medium">Belum ada history perpanjangan GPS.</p>
                                    @if(request('bulan') || request('tahun'))
                                        <p class="text-xs">Coba ubah filter periode.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
            <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
        </div>

        {{-- Table Footer --}}
        @if($data->count())
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
            <span>Menampilkan <strong class="text-gray-700">{{ $data->count() }}</strong> data</span>
            <span>Total: <strong class="text-gray-700">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong></span>
        </div>
        @endif

    </div>

</div>

<script>
    document.getElementById('search').addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#tableBody tr').forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
</script>

@endsection