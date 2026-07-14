@extends('admin.layouts.app')
@section('title', 'Kampanye Marketing')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kampanye Marketing</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola kampanye pemasaran perusahaan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kampanye.pdf') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Kampanye
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-bullhorn"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Kampanye</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 text-lg flex-shrink-0">
                <i class="fa fa-flag-checkered"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalSelesai }}</p>
                <p class="text-xs text-gray-500 mt-1">Selesai</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0">
                <i class="fa fa-clock"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDijadwalkan }}</p>
                <p class="text-xs text-gray-500 mt-1">Dijadwalkan</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Kampanye</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari kampanye..." oninput="onSearchInput(this.value)"
                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">ID</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nama Kampanye</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tipe</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Channel</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Target Segment</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Periode</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">PIC</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600">{{ $d->id_kampanye }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fa fa-bullhorn text-blue-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-xs">{{ $d->nama_kampanye }}</p>
                                    <p class="text-gray-400 text-xs">{{ Str::limit($d->isi_pesan_ringkas, 30) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-700 text-xs">{{ $d->tipe_kampanye }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                {{ $d->channel }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                {{ $d->target_segment }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($d->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($d->tanggal_akhir)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-700 text-xs">{{ $d->pic ?? '-' }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $sc = match($d->status) {
                                    'Aktif' => 'bg-green-100 text-green-700',
                                    'Selesai' => 'bg-gray-100 text-gray-600',
                                    'Dijadwalkan' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-red-100 text-red-600',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">
                                <i class="fa fa-circle text-[6px]"></i> {{ $d->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                                    data-id="{{ $d->id }}"
                                    onclick="triggerShow({{ $d->id }})">
                                    <i class="fa fa-eye text-xs"></i> Detail
                                </button>
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-id="{{ $d->id }}"
                                    data-action="{{ route('kampanye.update', $d->id) }}"
                                    data-id_kampanye="{{ $d->id_kampanye }}"
                                    data-nama_kampanye="{{ $d->nama_kampanye }}"
                                    data-tipe_kampanye="{{ $d->tipe_kampanye }}"
                                    data-channel="{{ $d->channel }}"
                                    data-target_segment="{{ $d->target_segment }}"
                                    data-tanggal_mulai="{{ $d->tanggal_mulai }}"
                                    data-tanggal_akhir="{{ $d->tanggal_akhir }}"
                                    data-subjek_pesan="{{ $d->subjek_pesan }}"
                                    data-isi_pesan_ringkas="{{ $d->isi_pesan_ringkas }}"
                                    data-status="{{ $d->status }}"
                                    data-pic="{{ $d->pic }}"
                                    onclick="triggerEdit(this)">
                                    <i class="fa fa-edit text-xs"></i> Edit
                                </button>
                                <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('kampanye.destroy', $d->id) }}"
                                    data-name="{{ $d->nama_kampanye }}"
                                    onclick="triggerDelete(this)">
                                    <i class="fa fa-trash text-xs"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fa fa-bullhorn text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Belum ada data kampanye</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT --}}
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Kampanye</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data kampanye dengan lengkap</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="mainForm" action="{{ route('kampanye.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">ID Kampanye <span class="text-red-500">*</span></label>
                    <input type="text" name="id_kampanye" id="f_id_kampanye" required placeholder="Contoh: MKT001"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" id="f_pic" required placeholder="Nama PIC"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Kampanye <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kampanye" id="f_nama_kampanye" required placeholder="Contoh: Promo Akhir Tahun"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe Kampanye <span class="text-red-500">*</span></label>
                    <select name="tipe_kampanye" id="f_tipe_kampanye" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Tipe -</option>
                        <option value="Promosi">Promosi</option>
                        <option value="Retensi">Retensi</option>
                        <option value="Awareness">Awareness</option>
                        <option value="Engagement">Engagement</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Channel <span class="text-red-500">*</span></label>
                    <select name="channel" id="f_channel" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Channel -</option>
                        <option value="Email">Email</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="SMS">SMS</option>
                        <option value="Social Media">Social Media</option>
                        <option value="Website">Website</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Target Segment <span class="text-red-500">*</span></label>
                <input type="text" name="target_segment" id="f_target_segment" required placeholder="Contoh: Pelanggan Aktif"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" id="f_tanggal_mulai" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Akhir <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_akhir" id="f_tanggal_akhir" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subjek Pesan <span class="text-red-500">*</span></label>
                <input type="text" name="subjek_pesan" id="f_subjek_pesan" required placeholder="Subject email/pesan"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Isi Pesan (Ringkas) <span class="text-red-500">*</span></label>
                <textarea name="isi_pesan_ringkas" id="f_isi_pesan_ringkas" rows="3" required placeholder="Isi pesan singkat..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select name="status" id="f_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih Status -</option>
                    <option value="Dijadwalkan">Dijadwalkan</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="fa fa-save text-sm"></i> Simpan Data
            </button>
        </form>
    </div>
</div>

{{-- MODAL SHOW / DETAIL --}}
<div id="showModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div>
                <h2 class="text-base font-bold text-gray-800">Detail Kampanye</h2>
                <p id="show_subtitle" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button onclick="closeShowModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="showLoading" class="px-6 py-12 text-center hidden">
            <i class="fa fa-spinner fa-spin text-2xl text-blue-400"></i>
            <p class="text-xs text-gray-400 mt-2">Memuat data...</p>
        </div>
        <div id="showContent" class="px-6 py-5 space-y-4 hidden">
            {{-- Badge Status --}}
            <div class="flex items-center justify-between">
                <span id="show_id_badge" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-semibold bg-blue-50 text-blue-700 border border-blue-100"></span>
                <span id="show_status_badge" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"></span>
            </div>
            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Tipe Kampanye</p>
                    <p id="show_tipe" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Channel</p>
                    <p id="show_channel" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Target Segment</p>
                    <p id="show_target_segment" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">PIC</p>
                    <p id="show_pic" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Tanggal Mulai</p>
                    <p id="show_tanggal_mulai" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Tanggal Akhir</p>
                    <p id="show_tanggal_akhir" class="text-sm font-semibold text-gray-800"></p>
                </div>
            </div>
            {{-- Pesan --}}
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide mb-1">Subjek Pesan</p>
                <p id="show_subjek" class="text-sm font-semibold text-gray-800 mb-3"></p>
                <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide mb-1">Isi Pesan</p>
                <p id="show_isi_pesan" class="text-sm text-gray-700 leading-relaxed"></p>
            </div>
            {{-- Footer info --}}
            <div class="flex items-center gap-4 text-xs text-gray-400 pt-1 border-t border-gray-100">
                <span><i class="fa fa-clock mr-1"></i>Dibuat: <span id="show_created_at"></span></span>
                <span><i class="fa fa-edit mr-1"></i>Diperbarui: <span id="show_updated_at"></span></span>
            </div>
        </div>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Kampanye?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">Batal</button>
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-trash text-xs"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- ALERT --}}
@if(session('success') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
            <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none mt-0.5"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('kampanye.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Kampanye';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){document.getElementById('modalTitle').innerText='Edit Kampanye';mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
document.getElementById('f_id_kampanye').value=btn.dataset.id_kampanye??'';document.getElementById('f_nama_kampanye').value=btn.dataset.nama_kampanye??'';
document.getElementById('f_tipe_kampanye').value=btn.dataset.tipe_kampanye??'';document.getElementById('f_channel').value=btn.dataset.channel??'';
document.getElementById('f_target_segment').value=btn.dataset.target_segment??'';document.getElementById('f_tanggal_mulai').value=btn.dataset.tanggal_mulai??'';
document.getElementById('f_tanggal_akhir').value=btn.dataset.tanggal_akhir??'';document.getElementById('f_subjek_pesan').value=btn.dataset.subjek_pesan??'';
document.getElementById('f_isi_pesan_ringkas').value=btn.dataset.isi_pesan_ringkas??'';document.getElementById('f_status').value=btn.dataset.status??'';
document.getElementById('f_pic').value=btn.dataset.pic??'';mainModal.classList.remove('hidden');mainModal.classList.add('flex');}

// Show Modal
const showModal=document.getElementById('showModal');
const showLoading=document.getElementById('showLoading');
const showContent=document.getElementById('showContent');

function triggerShow(id){
    showModal.classList.remove('hidden');showModal.classList.add('flex');
    showLoading.classList.remove('hidden');showContent.classList.add('hidden');
    fetch(`/admin/kampanye/${id}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json())
    .then(d=>{
        document.getElementById('show_subtitle').innerText=d.nama_kampanye;
        document.getElementById('show_id_badge').innerText=d.id_kampanye;
        // Status badge
        const sc={'Aktif':'bg-green-100 text-green-700','Selesai':'bg-gray-100 text-gray-600','Dijadwalkan':'bg-yellow-100 text-yellow-700'};
        const badge=document.getElementById('show_status_badge');
        badge.className='inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold '+(sc[d.status]||'bg-red-100 text-red-600');
        badge.innerHTML=`<i class="fa fa-circle text-[6px]"></i> ${d.status}`;
        // Fields
        document.getElementById('show_tipe').innerText=d.tipe_kampanye||'-';
        document.getElementById('show_channel').innerText=d.channel||'-';
        document.getElementById('show_target_segment').innerText=d.target_segment||'-';
        document.getElementById('show_pic').innerText=d.pic||'-';
        // Format dates
        const fmt=s=>s?new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}):'-';
        document.getElementById('show_tanggal_mulai').innerText=fmt(d.tanggal_mulai);
        document.getElementById('show_tanggal_akhir').innerText=fmt(d.tanggal_akhir);
        document.getElementById('show_subjek').innerText=d.subjek_pesan||'-';
        document.getElementById('show_isi_pesan').innerText=d.isi_pesan_ringkas||'-';
        const fmtDt=s=>s?new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}):'-';
        document.getElementById('show_created_at').innerText=fmtDt(d.created_at);
        document.getElementById('show_updated_at').innerText=fmtDt(d.updated_at);
        showLoading.classList.add('hidden');showContent.classList.remove('hidden');
    })
    .catch(()=>{showLoading.classList.add('hidden');showContent.classList.remove('hidden');});
}
function closeShowModal(){showModal.classList.add('hidden');showModal.classList.remove('flex');}
showModal.addEventListener('click',e=>{if(e.target===showModal)closeShowModal();});
const deleteModal=document.getElementById('deleteModal'),deleteForm=document.getElementById('deleteForm');
function triggerDelete(btn){deleteForm.action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name;deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});

(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);o.addEventListener('click',e=>{if(e.target===o)closeAlert();});function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
@endpush
