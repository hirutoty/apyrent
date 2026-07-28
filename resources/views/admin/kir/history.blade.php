@extends('admin.layouts.app')

@section('title', 'History KIR Kendaraan')

@section('content')

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        {{-- Header --}}
        <div class="flex flex-col gap-4 p-6 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">History Perpanjangan KIR</h1>
                    <p class="text-sm text-gray-500 mt-1">Riwayat seluruh data KIR kendaraan yang telah diperpanjang.</p>
                </div>
            </div>

            {{-- Filter bulan & tahun + search + export PDF --}}
            <form method="GET" action="{{ route('history.kir.index') }}" class="flex flex-wrap items-end gap-3">
                {{-- Search --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-500 uppercase">Cari</label>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Kendaraan, no uji..."
                            class="pl-8 pr-3 py-2 w-56 rounded-lg border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <select name="bulan" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="semua" {{ $bulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                    @php
                        $namaBulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                    @endphp
                    @foreach($namaBulanList as $no => $nama)
                        <option value="{{ $no }}" {{ (string) $bulan === (string) $no ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>

                <select name="tahun" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="semua" {{ $tahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ (string) $tahun === (string) $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>

                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fa fa-search text-xs"></i> Cari
                </button>

                @if(request('search') || ($bulan != 'semua') || ($tahun != 'semua'))
                    <a href="{{ route('history.kir.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <i class="fa fa-rotate-left text-xs"></i> Reset
                    </a>
                @endif

                <a href="{{ route('history.kir.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fa fa-file-pdf"></i> Export PDF
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Kendaraan</th>
                        <th class="px-4 py-3 text-left">Nopol</th>
                        <th class="px-4 py-3 text-left">No Uji</th>
                        <th class="px-4 py-3 text-right">Biaya</th>
                        <th class="px-4 py-3 text-center">Masa Berlaku Lama</th>
                        <th class="px-4 py-3 text-center">Diperpanjang</th>
                        <th class="px-4 py-3 text-center">Bukti</th>
                        <th class="px-4 py-3 text-center">Lampiran</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $item)
                        <tr class="border-b hover:bg-gray-50">

                            <td class="px-4 py-3">{{ $data->firstItem() + $loop->index }}</td>

                            <td class="px-4 py-3">{{ $item->kendaraan->merk ?? '-' }}</td>

                            <td class="px-4 py-3">{{ $item->kendaraan->nopol ?? '-' }}</td>

                            <td class="px-4 py-3">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">
                                    {{ $item->no_uji }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                Rp {{ number_format($item->biaya, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ \Carbon\Carbon::parse($item->masa_berlaku)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ \Carbon\Carbon::parse($item->kir->tanggal_bayar)->format('d M Y') }}
                            </td>

                            <td>
                                    @if ($item->image)
                                        @php
                                            $filename = basename($item->image);
                                        @endphp

                                        <a href="{{ asset($item->image) }}" target="_blank"
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
                                <form action="{{ route('history.kir.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus history ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-gray-400">
                                Belum ada history perpanjangan KIR.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-3 border-t border-gray-100 px-5">
                <x-pagination :paginator="$data" />
            </div>
        </div>

    </div>

@endsection