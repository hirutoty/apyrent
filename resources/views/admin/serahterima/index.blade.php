@extends('admin.layouts.app')

@section('title', 'Serah Terima')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Serah Terima</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data serah terima kendaraan</p>
        </div>
        <button onclick="openModalTambah()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah Serah Terima
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Data</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $totalBooking }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fa fa-file-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">File Masuk</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $totalFileMasuk }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="fa fa-file-import text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">File Keluar</p>
                    <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ $totalFileKeluar }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i class="fa fa-file-export text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- TAB NAVIGATION --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex border-b border-gray-200">
            <a href="{{ route('serahterima.index', ['tab' => 'booking']) }}"
               class="px-6 py-3 text-sm font-medium {{ $tab === 'booking' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">
                <i class="fa fa-calendar-check mr-2"></i>Booking
            </a>
            <a href="{{ route('serahterima.index', ['tab' => 'kontrak']) }}"
               class="px-6 py-3 text-sm font-medium {{ $tab === 'kontrak' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">
                <i class="fa fa-file-contract mr-2"></i>Kontrak
            </a>
        </div>

        {{-- SEARCH BAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">
                    {{ $tab === 'booking' ? 'Serah Terima Booking' : 'Serah Terima Kontrak' }}
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $tab === 'booking' ? $dataBooking->total() : $dataKontrak->total() }} total data
                </p>
            </div>
            <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" name="search" placeholder="Cari tanggal, nopol, penyewa..."
                        value="{{ request('search') }}"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fa fa-search text-xs"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('serahterima.index', ['tab' => $tab]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-rotate-left text-xs"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- TABLE BOOKING --}}
        @if($tab === 'booking')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Penyewa</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">File Masuk</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">File Keluar</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataBooking as $d)
                        <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100">
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $dataBooking->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-medium text-gray-700">{{ $d->tanggal->format('d M Y') }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-car text-blue-400 text-xs"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-800">{{ $d->rental->kendaraan->nopol ?? '-' }}</span>
                                        <span class="text-xs text-gray-500">{{ $d->rental->kendaraan->merk ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($d->rental->member->nama ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $d->rental->member->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($d->file_masuk)
                                    <a href="{{ Storage::url($d->file_masuk) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                        <i class="fa fa-file text-xs"></i>
                                        <span class="font-medium">{{ basename($d->file_masuk) }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($d->file_keluar)
                                    <a href="{{ Storage::url($d->file_keluar) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                        <i class="fa fa-file text-xs"></i>
                                        <span class="font-medium">{{ basename($d->file_keluar) }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="btn-edit-booking inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-id_rental="{{ $d->id_rental }}"
                                        data-tanggal="{{ $d->tanggal->format('Y-m-d') }}"
                                        data-file_masuk="{{ $d->file_masuk ? 'Ada' : '' }}"
                                        data-file_keluar="{{ $d->file_keluar ? 'Ada' : '' }}">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('serahterima.booking.destroy', $d->id) }}" method="POST"
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
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-file-alt text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data serah terima booking</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Serah Terima" untuk menambahkan data baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($dataBooking->isNotEmpty())
            <div class="py-3 border-t border-gray-100">
                <x-pagination :paginator="$dataBooking" />
            </div>
            @endif
        </div>
        @endif

        {{-- TABLE KONTRAK --}}
        @if($tab === 'kontrak')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No Kontrak</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Penyewa</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">File Masuk</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">File Keluar</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataKontrak as $d)
                        <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100">
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $dataKontrak->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-medium text-gray-700">{{ $d->tanggal->format('d M Y') }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm text-gray-700">{{ $d->kontrak->no_kontrak ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-car text-blue-400 text-xs"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-800">{{ $d->rental->kendaraan->nopol ?? '-' }}</span>
                                        <span class="text-xs text-gray-500">{{ $d->rental->kendaraan->merk ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($d->rental->member->nama ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $d->rental->member->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($d->file_masuk)
                                    <a href="{{ Storage::url($d->file_masuk) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                        <i class="fa fa-file text-xs"></i>
                                        <span class="font-medium">{{ basename($d->file_masuk) }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($d->file_keluar)
                                    <a href="{{ Storage::url($d->file_keluar) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                        <i class="fa fa-file text-xs"></i>
                                        <span class="font-medium">{{ basename($d->file_keluar) }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="btn-edit-kontrak inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-idkontrak="{{ $d->idkontrak }}"
                                        data-id_rental="{{ $d->id_rental }}"
                                        data-tanggal="{{ $d->tanggal->format('Y-m-d') }}"
                                        data-file_masuk="{{ $d->file_masuk ? 'Ada' : '' }}"
                                        data-file_keluar="{{ $d->file_keluar ? 'Ada' : '' }}">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('serahterima.kontrak.destroy', $d->id) }}" method="POST"
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
                                        <i class="fa fa-file-contract text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data serah terima kontrak</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Serah Terima" untuk menambahkan data baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($dataKontrak->isNotEmpty())
            <div class="py-3 border-t border-gray-100">
                <x-pagination :paginator="$dataKontrak" />
            </div>
            @endif
        </div>
        @endif

    </div>

</div>

{{-- MODAL TAMBAH/EDIT BOOKING --}}
<div id="modalBooking"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 id="modalBookingTitle" class="text-base font-bold text-gray-800">Tambah Serah Terima Booking</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data serah terima booking</p>
            </div>
            <button onclick="closeModalBooking()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="formBooking" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div id="method-container-booking"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rental <span class="text-red-500">*</span></label>
                <select name="id_rental" id="id_rental" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">-- Pilih Rental --</option>
                    @foreach($rentals as $r)
                        <option value="{{ $r->id }}">{{ $r->kendaraan->nopol ?? '-' }} - {{ $r->member->nama ?? '-' }} ({{ $r->tanggal_mulai }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="tanggal_booking" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Masuk</label>
                <input type="file" name="file_masuk" id="file_masuk_booking" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <p class="text-xs text-gray-400 mt-1">PDF, JPG, JPEG, PNG (Max: 5MB)</p>
                <p id="file_masuk_info" class="text-xs text-green-600 mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Keluar</label>
                <input type="file" name="file_keluar" id="file_keluar_booking" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <p class="text-xs text-gray-400 mt-1">PDF, JPG, JPEG, PNG (Max: 5MB)</p>
                <p id="file_keluar_info" class="text-xs text-green-600 mt-1 hidden"></p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModalBooking()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan
                </button>
            </div>
        </form>

    </div>
</div>

{{-- MODAL TAMBAH/EDIT KONTRAK --}}
<div id="modalKontrak"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 id="modalKontrakTitle" class="text-base font-bold text-gray-800">Tambah Serah Terima Kontrak</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data serah terima kontrak</p>
            </div>
            <button onclick="closeModalKontrak()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="formKontrak" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div id="method-container-kontrak"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontrak (Optional)</label>
                <select name="idkontrak" id="idkontrak"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">-- Pilih Kontrak (Opsional) --</option>
                    @foreach($kontraks as $k)
                        <option value="{{ $k->id }}">{{ $k->no_kontrak }} - {{ $k->kendaraan->nopol ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rental <span class="text-red-500">*</span></label>
                <select name="id_rental" id="id_rental_kontrak" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">-- Pilih Rental --</option>
                    @foreach($rentals as $r)
                        <option value="{{ $r->id }}">{{ $r->kendaraan->nopol ?? '-' }} - {{ $r->member->nama ?? '-' }} ({{ $r->tanggal_mulai }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="tanggal_kontrak" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Masuk</label>
                <input type="file" name="file_masuk" id="file_masuk_kontrak" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <p class="text-xs text-gray-400 mt-1">PDF, JPG, JPEG, PNG (Max: 5MB)</p>
                <p id="file_masuk_info_kontrak" class="text-xs text-green-600 mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Keluar</label>
                <input type="file" name="file_keluar" id="file_keluar_kontrak" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <p class="text-xs text-gray-400 mt-1">PDF, JPG, JPEG, PNG (Max: 5MB)</p>
                <p id="file_keluar_info_kontrak" class="text-xs text-green-600 mt-1 hidden"></p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModalKontrak()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan
                </button>
            </div>
        </form>

    </div>
</div>

{{-- POPUP ALERT --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">

        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                    @if(session('error'))
                        <li>{{ session('error') }}</li>
                    @endif
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button onclick="closeAlert()"
                class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
            <i class="fa fa-times"></i>
        </button>

    </div>
</div>
@endif

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
const currentTab = '{{ $tab }}';
const modalBooking = document.getElementById('modalBooking');
const modalKontrak = document.getElementById('modalKontrak');

// ── MODAL BOOKING ──────────────────────────────────
function openModalTambah() {
    if (currentTab === 'booking') {
        openModalBooking();
    } else {
        openModalKontrak();
    }
}

function openModalBooking() {
    document.getElementById('modalBookingTitle').innerText = 'Tambah Serah Terima Booking';
    document.getElementById('formBooking').action = '{{ route("serahterima.booking.store") }}';
    document.getElementById('formBooking').reset();
    document.getElementById('method-container-booking').innerHTML = '';
    document.getElementById('file_masuk_info').classList.add('hidden');
    document.getElementById('file_keluar_info').classList.add('hidden');
    modalBooking.classList.remove('hidden');
    modalBooking.classList.add('flex');
}

function closeModalBooking() {
    modalBooking.classList.add('hidden');
    modalBooking.classList.remove('flex');
}

modalBooking.addEventListener('click', function (e) {
    if (e.target === modalBooking) closeModalBooking();
});

document.querySelectorAll('.btn-edit-booking').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('modalBookingTitle').innerText = 'Edit Serah Terima Booking';
        document.getElementById('formBooking').action = '/admin/serahterima/booking/' + this.dataset.id;
        document.getElementById('method-container-booking').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('id_rental').value = this.dataset.id_rental;
        document.getElementById('tanggal_booking').value = this.dataset.tanggal;
        
        // Info file existing
        if (this.dataset.file_masuk) {
            document.getElementById('file_masuk_info').innerText = '✓ File masuk sudah ada';
            document.getElementById('file_masuk_info').classList.remove('hidden');
        }
        if (this.dataset.file_keluar) {
            document.getElementById('file_keluar_info').innerText = '✓ File keluar sudah ada';
            document.getElementById('file_keluar_info').classList.remove('hidden');
        }
        
        modalBooking.classList.remove('hidden');
        modalBooking.classList.add('flex');
    });
});

// ── MODAL KONTRAK ──────────────────────────────────
function openModalKontrak() {
    document.getElementById('modalKontrakTitle').innerText = 'Tambah Serah Terima Kontrak';
    document.getElementById('formKontrak').action = '{{ route("serahterima.kontrak.store") }}';
    document.getElementById('formKontrak').reset();
    document.getElementById('method-container-kontrak').innerHTML = '';
    document.getElementById('file_masuk_info_kontrak').classList.add('hidden');
    document.getElementById('file_keluar_info_kontrak').classList.add('hidden');
    modalKontrak.classList.remove('hidden');
    modalKontrak.classList.add('flex');
}

function closeModalKontrak() {
    modalKontrak.classList.add('hidden');
    modalKontrak.classList.remove('flex');
}

modalKontrak.addEventListener('click', function (e) {
    if (e.target === modalKontrak) closeModalKontrak();
});

document.querySelectorAll('.btn-edit-kontrak').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('modalKontrakTitle').innerText = 'Edit Serah Terima Kontrak';
        document.getElementById('formKontrak').action = '/admin/serahterima/kontrak/' + this.dataset.id;
        document.getElementById('method-container-kontrak').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('idkontrak').value = this.dataset.idkontrak || '';
        document.getElementById('id_rental_kontrak').value = this.dataset.id_rental;
        document.getElementById('tanggal_kontrak').value = this.dataset.tanggal;
        
        // Info file existing
        if (this.dataset.file_masuk) {
            document.getElementById('file_masuk_info_kontrak').innerText = '✓ File masuk sudah ada';
            document.getElementById('file_masuk_info_kontrak').classList.remove('hidden');
        }
        if (this.dataset.file_keluar) {
            document.getElementById('file_keluar_info_kontrak').innerText = '✓ File keluar sudah ada';
            document.getElementById('file_keluar_info_kontrak').classList.remove('hidden');
        }
        
        modalKontrak.classList.remove('hidden');
        modalKontrak.classList.add('flex');
    });
});

// ── ESCAPE KEY ──────────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeModalBooking();
        closeModalKontrak();
    }
});

// ── POPUP ALERT ──────────────────────────────────
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function () {
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform = 'translateY(0)';
    }, 80);

    var timer = setTimeout(closeAlert, 4500);

    overlay.addEventListener('click', function (e) {
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
