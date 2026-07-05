@extends('admin.layouts.app')

@section('title', 'Data Lunas')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Data Lunas
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Daftar invoice yang sudah dibayar
            </p>
        </div>

        <div class="px-4 py-2 rounded-xl bg-green-100 text-green-700 font-semibold text-sm">
            Total Lunas : {{ $data->count() }}
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            <i class="fa-solid fa-circle-check mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-700">
                Daftar Pembayaran
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left">No</th>
                        <th class="px-5 py-4 text-left">Invoice</th>
                        <th class="px-5 py-4 text-left">Customer</th>
                        <th class="px-5 py-4 text-left">Total</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Bukti Bayar</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($data as $d)

                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4 font-semibold text-slate-800">
                                {{ $d->invoice->invoice_no }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $d->member->nama_member }}
                            </td>

            

                            <td class="px-5 py-4 font-semibold text-green-600">
                                Rp {{ number_format($d->total,0,',','.') }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Bayar
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
    @if($d->bukti)
        <a href="{{ asset('bukti/' . $d->bukti) }}"
            target="_blank"
            class="text-blue-600 hover:underline text-xs font-medium">
            <i class="fa-solid fa-image"></i>
            {{ $d->bukti }}
        </a>
    @else
        <span class="text-slate-400 text-xs">Tidak ada bukti</span>
    @endif
</td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center py-14 text-slate-400">
                                <i class="fa-solid fa-box-open text-5xl mb-3 block text-slate-300"></i>
                                <p class="font-semibold text-lg">
                                    Belum ada data lunas.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection