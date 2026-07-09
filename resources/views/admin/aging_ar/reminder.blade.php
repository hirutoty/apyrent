@extends('admin.layouts.app')

@section('title', 'Reminder Aging AR')

@section('content')

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Reminder Aging AR
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar piutang yang perlu ditindaklanjuti
                </p>
            </div>

            <div class="px-4 py-2 rounded-xl bg-amber-100 text-amber-700 font-semibold text-sm">
                Total Reminder : {{ $data->count() }}
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Header Table --}}
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-700">
                    Daftar Piutang
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left">No</th>
                            <th class="px-5 py-4 text-left">Invoice</th>
                            <th class="px-5 py-4 text-left">Customer</th>
                            <th class="px-5 py-4 text-left">Email</th>
                            <th class="px-5 py-4 text-left">Kontak</th>
                            <th class="px-5 py-4 text-left">Total</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse($data as $d)
                            <tr class="hover:bg-slate-50">

                                <td class="px-5 py-4">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    {{ $d->invoice->invoice_no }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $d->member->nama_pelanggan }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $d->member->email_pelanggan?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $d->member->kontak_pelanggan?? '-' }}
                                </td>

                                <td class="px-5 py-4 font-semibold text-red-600">
                                    Rp {{ number_format($d->total, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-center">

                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">

                                        <i class="fa-solid fa-circle-exclamation"></i>

                                        Belum Lunas

                                    </span>

                                </td>

                                

                                <td class="px-5 py-4 text-center">
                                    @if ($d->status == 'Belum Bayar')
                                        {{-- Tombol buka modal --}}
                                        <button type="button"
                                            onclick="document.getElementById('modal-bayar-{{ $d->id }}').classList.remove('hidden')"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition">
                                            <i class="fa-solid fa-money-bill"></i>
                                            Bayar
                                        </button>

                                        {{-- Modal Upload Bukti --}}
                                        <div id="modal-bayar-{{ $d->id }}"
                                            class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

                                            <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 relative">

                                                <button type="button"
                                                    onclick="document.getElementById('modal-bayar-{{ $d->id }}').classList.add('hidden')"
                                                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                                                    <i class="fa-solid fa-xmark text-lg"></i>
                                                </button>

                                                <h3 class="text-lg font-bold text-slate-800 mb-1">
                                                    Konfirmasi Pembayaran
                                                </h3>
                                                <p class="text-sm text-slate-500 mb-4">
                                                    Invoice: <strong>{{ $d->invoice->invoice_no }}</strong>
                                                </p>

                                                <form action="{{ route('aging_ar.bayar', $d->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf

                                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                                        Upload Bukti Bayar
                                                    </label>

                                                    <input type="file" name="bukti" required
                                                        class="block w-full text-sm border border-slate-300 rounded-lg cursor-pointer mb-1
                               file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                                                    @error('bukti')
                                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                    @enderror

                                                    <div class="flex justify-end gap-2 mt-5">
                                                        <button type="button"
                                                            onclick="document.getElementById('modal-bayar-{{ $d->id }}').classList.add('hidden')"
                                                            class="px-4 py-2 rounded-lg text-sm font-medium border border-slate-300 text-slate-600 hover:bg-slate-50">
                                                            Batal
                                                        </button>
                                                        <button type="submit"
                                                            class="px-4 py-2 rounded-lg text-sm font-medium bg-green-600 hover:bg-green-700 text-white">
                                                            <i class="fa-solid fa-upload"></i>
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <form action="{{ route('aging_ar.lunas.index', $d->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin invoice ini sudah lunas?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition">
                                                <i class="fa-solid fa-circle-check"></i>
                                                Tandai Lunas
                                            </button>
                                        </form>
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-14 text-slate-400">

                                    <i class="fa-solid fa-circle-check text-5xl mb-3 block text-green-500"></i>

                                    <p class="font-semibold text-lg">
                                        Tidak ada reminder.
                                    </p>

                                    <p class="text-sm">
                                        Semua piutang sudah lunas.
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

