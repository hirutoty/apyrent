@extends('admin.layouts.app')
@section('title', 'Detail Pembayaran - ' . $pembayaran->no_pr)

@section('content')
<div class="space-y-6 p-5">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('pembayaran.index') }}"
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                <i class="fa fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pembayaran</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $pembayaran->no_pr }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Status Badge --}}
            @php
                $statusColor = match($pembayaran->status) {
                    'Disetujui'          => 'bg-green-100 text-green-700 border-green-200',
                    'Ditolak'            => 'bg-red-100 text-red-700 border-red-200',
                    'Diajukan'           => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                    'Pending'            => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'Disetujui Sebagian' => 'bg-teal-100 text-teal-700 border-teal-200',
                    default              => 'bg-gray-100 text-gray-600 border-gray-200',
                };
                $statusIcon = match($pembayaran->status) {
                    'Disetujui'          => 'fa-check-circle',
                    'Ditolak'            => 'fa-times-circle',
                    'Diajukan'           => 'fa-paper-plane',
                    'Pending'            => 'fa-clock',
                    'Disetujui Sebagian' => 'fa-check',
                    default              => 'fa-circle',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-semibold border {{ $statusColor }}">
                <i class="fa {{ $statusIcon }} text-xs"></i>
                {{ $pembayaran->status }}
            </span>

            {{-- Action Buttons --}}
            @if(in_array($pembayaran->status, ['Pending', 'Diajukan']))
                @if($role !== 'superadmin')
                    <a href="{{ route('pembayaran.edit', $pembayaran) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors">
                        <i class="fa fa-edit text-xs"></i> Edit
                    </a>
                @endif
            @endif

            @if($pembayaran->status === 'Pending' && $role !== 'superadmin')
                <form action="{{ route('pembayaran.ajukan', $pembayaran) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors"
                        onclick="return confirm('Ajukan pembayaran ini?')">
                        <i class="fa fa-paper-plane text-xs"></i> Ajukan
                    </button>
                </form>
            @endif

            @if($role === 'superadmin' && in_array($pembayaran->status, ['Pending', 'Diajukan']))
                <button type="button"
                    onclick="openSingleApproveModal({{ $pembayaran->id }}, '{{ $pembayaran->no_pr }}')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors">
                    <i class="fa fa-check text-xs"></i> Approve
                </button>
                <button type="button"
                    onclick="openSingleRejectModal({{ $pembayaran->id }}, '{{ $pembayaran->no_pr }}')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">
                    <i class="fa fa-times text-xs"></i> Reject
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: Info Utama --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Pengajuan --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa fa-file-alt text-blue-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Informasi Pengajuan</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">No. PR</p>
                            <p class="text-sm font-mono font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded inline-block">
                                {{ $pembayaran->no_pr }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tanggal</p>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $pembayaran->tanggal ? \Carbon\Carbon::parse($pembayaran->tanggal)->translatedFormat('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tipe</p>
                            <p class="text-sm font-medium text-gray-700 capitalize">
                                {{ $pembayaran->source_type_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Departemen</p>
                            <p class="text-sm font-medium text-gray-700">
                                <i class="fa fa-building text-blue-400 text-[10px] mr-1"></i>
                                {{ $pembayaran->departemen ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Pemohon</p>
                            <p class="text-sm font-medium text-gray-700">{{ $pembayaran->pemohon ?? '-' }}</p>
                        </div>
                        @if($pembayaran->terakhir_diajukan)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Terakhir Diajukan</p>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $pembayaran->terakhir_diajukan->format('d M Y H:i') }}
                            </p>
                        </div>
                        @endif
                        @if($pembayaran->keterangan)
                        <div class="col-span-2 md:col-span-3">
                            <p class="text-xs text-gray-400 mb-0.5">Keterangan</p>
                            <p class="text-sm text-gray-700">{{ $pembayaran->keterangan }}</p>
                        </div>
                        @endif
                        @if($pembayaran->alasan_permintaan)
                        <div class="col-span-2 md:col-span-3">
                            <p class="text-xs text-gray-400 mb-0.5">Alasan Permintaan</p>
                            <p class="text-sm text-gray-700">{{ $pembayaran->alasan_permintaan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tipe Service: Info Service --}}
            @if($pembayaran->tipe_pembayaran === 'service' || $pembayaran->kendaraan_id)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center">
                        <i class="bi bi-tools text-orange-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Informasi Service</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($pembayaran->kendaraan)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kendaraan</p>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $pembayaran->kendaraan->nopol ?? '-' }}
                                @if($pembayaran->kendaraan->merk)
                                    <span class="text-gray-400">· {{ $pembayaran->kendaraan->merk }}</span>
                                @endif
                            </p>
                        </div>
                        @endif
                        @if($pembayaran->tanggal_service)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tanggal Service</p>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $pembayaran->tanggal_service->translatedFormat('d F Y') }}
                            </p>
                        </div>
                        @endif
                        @if($pembayaran->kilometer)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kilometer</p>
                            <p class="text-sm font-medium text-gray-700">{{ number_format($pembayaran->kilometer, 0, ',', '.') }} km</p>
                        </div>
                        @endif
                        @if($pembayaran->keluhan)
                        <div class="col-span-2 md:col-span-3">
                            <p class="text-xs text-gray-400 mb-0.5">Keluhan</p>
                            <p class="text-sm text-gray-700">{{ $pembayaran->keluhan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Items (Belanja) --}}
            @if($pembayaran->items->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <i class="fa fa-boxes text-emerald-600 text-xs"></i>
                        </span>
                        <h2 class="text-sm font-bold text-gray-800">Item Pembelian</h2>
                        <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                            {{ $pembayaran->items->count() }} item
                        </span>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">
                        Total: Rp {{ number_format($pembayaran->items->sum('subtotal'), 0, ',', '.') }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">#</th>
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Nama Barang</th>
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Kategori</th>
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Merk</th>
                                <th class="text-center text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Qty</th>
                                <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Harga Satuan</th>
                                <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaran->items as $idx => $item)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-medium text-gray-800">{{ $item->nama_barang }}</p>
                                    @if($item->spesifikasi)
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $item->spesifikasi }}</p>
                                    @endif
                                    @if($item->keterangan)
                                        <p class="text-[11px] text-gray-400 mt-0.5 italic">{{ $item->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $item->kategori ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $item->merk ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-xs text-gray-700">
                                    {{ $item->qty }} {{ $item->satuan }}
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-gray-700">
                                    Rp {{ $item->harga_satuan ? number_format($item->harga_satuan, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right text-xs font-semibold text-emerald-600">
                                    Rp {{ $item->subtotal ? number_format($item->subtotal, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                <td colspan="6" class="px-4 py-3 text-sm font-bold text-gray-700 text-right">Total</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-emerald-700">
                                    Rp {{ number_format($pembayaran->items->sum('subtotal'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @elseif($pembayaran->serviceParts->count() > 0)
            {{-- Service Parts --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center">
                            <i class="bi bi-tools text-orange-600 text-xs"></i>
                        </span>
                        <h2 class="text-sm font-bold text-gray-800">Part Service</h2>
                        <span class="text-xs font-semibold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                            {{ $pembayaran->serviceParts->count() }} part
                        </span>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">
                        Total: Rp {{ number_format($pembayaran->serviceParts->sum('biaya'), 0, ',', '.') }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">#</th>
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Nama Part</th>
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Informasi Bank</th>
                                <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Keterangan</th>
                                <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaran->serviceParts as $idx => $part)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 text-xs font-medium text-gray-800">{{ $part->nama_part ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($part->nama_bank || $part->no_rekening || $part->nama_rekening)
                                    <div class="flex flex-col gap-0.5 text-[11px] text-gray-500">
                                        @if($part->nama_bank)
                                        <span><i class="fa fa-building text-[9px] text-gray-400 mr-1"></i>{{ $part->nama_bank }}</span>
                                        @endif
                                        @if($part->no_rekening)
                                        <span class="font-mono">{{ $part->no_rekening }}</span>
                                        @endif
                                        @if($part->nama_rekening)
                                        <span>a/n {{ $part->nama_rekening }}</span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $part->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-xs font-semibold text-emerald-600">
                                    Rp {{ $part->biaya ? number_format($part->biaya, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-700 text-right">Total</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-emerald-700">
                                    Rp {{ number_format($pembayaran->serviceParts->sum('biaya'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @else
            {{-- Legacy single-item --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fa fa-shopping-cart text-emerald-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Detail Barang / Jasa</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($pembayaran->barang_jasa)
                        <div class="col-span-2 md:col-span-3">
                            <p class="text-xs text-gray-400 mb-0.5">Nama Barang / Jasa</p>
                            <p class="text-sm font-medium text-gray-700">{{ $pembayaran->barang_jasa }}</p>
                        </div>
                        @endif
                        @if($pembayaran->kode_barang)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kode Barang</p>
                            <p class="text-sm font-mono text-gray-700">{{ $pembayaran->kode_barang }}</p>
                        </div>
                        @endif
                        @if($pembayaran->qty)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Qty</p>
                            <p class="text-sm font-medium text-gray-700">{{ $pembayaran->qty }} {{ $pembayaran->satuan }}</p>
                        </div>
                        @endif
                        @if($pembayaran->nominal)
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Nominal</p>
                            <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Source Data (Pengeluaran otomatis) --}}
            @if($pembayaran->source_type && $pembayaran->source_data)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <i class="fa fa-link text-indigo-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Data Sumber — {{ $pembayaran->source_type_name }}</h2>
                </div>
                <div class="p-5">
                    @php $sd = $pembayaran->source_data; @endphp
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($sd as $key => $val)
                            @if(!is_array($val) && $val !== null && $val !== '')
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5 capitalize">{{ str_replace('_', ' ', $key) }}</p>
                                <p class="text-sm text-gray-700">{{ $val }}</p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    {{-- GPS items if any --}}
                    @if(!empty($sd['gps_items']) && is_array($sd['gps_items']))
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="text-left px-3 py-2 text-gray-400 font-semibold">Item</th>
                                    <th class="text-right px-3 py-2 text-gray-400 font-semibold">Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sd['gps_items'] as $gi)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-2 text-gray-700">{{ $gi['nama'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right text-emerald-600 font-medium">
                                        Rp {{ isset($gi['biaya']) ? number_format($gi['biaya'], 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Approval History --}}
            @if($pembayaran->approvals->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="fa fa-history text-purple-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Riwayat Approval</h2>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        @foreach($pembayaran->approvals as $approval)
                        @php
                            $isApproved = in_array($approval->action, ['approved', 'Disetujui', 'approved_partial']);
                            $isRejected = in_array($approval->action, ['rejected', 'Ditolak']);
                            $approvalColor = $isApproved ? 'bg-green-50 border-green-200' : ($isRejected ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200');
                            $approvalIcon  = $isApproved ? 'fa-check-circle text-green-500' : ($isRejected ? 'fa-times-circle text-red-500' : 'fa-info-circle text-blue-500');
                            $approvalLabel = match($approval->action) {
                                'approved'         => 'Disetujui',
                                'approved_partial' => 'Disetujui Sebagian',
                                'rejected'         => 'Ditolak',
                                'submitted'        => 'Diajukan',
                                default            => ucfirst($approval->action),
                            };
                        @endphp
                        <div class="flex gap-3 p-3 rounded-xl border {{ $approvalColor }}">
                            <i class="fa {{ $approvalIcon }} mt-0.5 flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-gray-800">{{ $approvalLabel }}</p>
                                    <p class="text-[11px] text-gray-400">
                                        {{ $approval->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @if($approval->user)
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <i class="fa fa-user text-[10px] mr-1"></i>{{ $approval->user->nama ?? $approval->user->name }}
                                    </p>
                                @endif
                                @if($approval->catatan)
                                    <p class="text-xs text-gray-600 mt-1 italic">"{{ $approval->catatan }}"</p>
                                @endif
                                {{-- Bukti files --}}
                                @if($approval->bukti_files && count($approval->bukti_files) > 0)
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($approval->bukti_files as $file)
                                    @php $filePath = is_array($file) ? ($file['path'] ?? '') : $file; @endphp
                                    @if($filePath)
                                        @php $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)); @endphp
                                        @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                            <a href="/{{ $filePath }}" target="_blank" class="inline-block">
                                                <img src="/{{ $filePath }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition-opacity" alt="Bukti">
                                            </a>
                                        @else
                                            <a href="/{{ $filePath }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg border border-blue-200 transition-colors">
                                                <i class="fa fa-file text-[10px]"></i>
                                                {{ basename($filePath) }}
                                            </a>
                                        @endif
                                    @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN: Sidebar Info --}}
        <div class="space-y-5">

            {{-- Ringkasan Nominal --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fa fa-money-bill-wave text-emerald-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Ringkasan</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Total Nominal</p>
                        <p class="text-2xl font-bold text-emerald-600">
                            Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-400 mb-1">Status</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold border {{ $statusColor }}">
                            <i class="fa {{ $statusIcon }} text-[10px]"></i>
                            {{ $pembayaran->status }}
                        </span>
                    </div>
                    @if($pembayaran->disetujui_oleh)
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-400 mb-1">Disetujui Oleh</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pembayaran->disetujui_oleh }}</p>
                        @if($pembayaran->tanggal_persetujuan)
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($pembayaran->tanggal_persetujuan)->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                    @endif
                    @if($pembayaran->catatan)
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-400 mb-1">Catatan Approval</p>
                        <p class="text-sm text-gray-700 italic">"{{ $pembayaran->catatan }}"</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Info Rekening --}}
            @if($pembayaran->nama_bank || $pembayaran->no_rekening)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa fa-university text-blue-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Info Rekening</h2>
                </div>
                <div class="p-5 space-y-3">
                    @if($pembayaran->nama_bank)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Bank</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pembayaran->nama_bank }}</p>
                    </div>
                    @endif
                    @if($pembayaran->no_rekening)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. Rekening</p>
                        <p class="text-sm font-mono font-medium text-gray-700">{{ $pembayaran->no_rekening }}</p>
                    </div>
                    @endif
                    @if($pembayaran->nama_rekening)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Atas Nama</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pembayaran->nama_rekening }}</p>
                    </div>
                    @endif
                    @if($pembayaran->nama_penerima)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nama Penerima</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pembayaran->nama_penerima }}</p>
                    </div>
                    @endif
                    @if($pembayaran->informasi)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Informasi Tambahan</p>
                        <p class="text-sm text-gray-700">{{ $pembayaran->informasi }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Supplier --}}
            @if($pembayaran->supplier)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center">
                        <i class="fa fa-truck text-violet-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Supplier</h2>
                </div>
                <div class="p-5 space-y-2">
                    <p class="text-sm font-semibold text-gray-800">{{ $pembayaran->supplier->nama_supplier }}</p>
                    @if($pembayaran->supplier->no_telp)
                        <p class="text-xs text-gray-500">
                            <i class="fa fa-phone text-[10px] mr-1"></i>{{ $pembayaran->supplier->no_telp }}
                        </p>
                    @endif
                    @if($pembayaran->supplier->nama_marketing)
                        <p class="text-xs text-gray-500">
                            <i class="fa fa-user text-[10px] mr-1"></i>{{ $pembayaran->supplier->nama_marketing }}
                            @if($pembayaran->supplier->kontak_marketing)
                                <span class="ml-1 text-gray-400">({{ $pembayaran->supplier->kontak_marketing }})</span>
                            @endif
                        </p>
                    @endif
                    @if($pembayaran->supplier->alamat)
                        <p class="text-xs text-gray-500">
                            <i class="fa fa-map-marker-alt text-[10px] mr-1"></i>{{ $pembayaran->supplier->alamat }}
                        </p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Bukti Pembayaran --}}
            @if($pembayaran->bukti_pembayaran)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center">
                        <i class="fa fa-receipt text-teal-600 text-xs"></i>
                    </span>
                    <h2 class="text-sm font-bold text-gray-800">Bukti Pembayaran</h2>
                </div>
                <div class="p-5">
                    @php $ext = strtolower(pathinfo($pembayaran->bukti_pembayaran, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                        <a href="/{{ $pembayaran->bukti_pembayaran }}" target="_blank">
                            <img src="/{{ $pembayaran->bukti_pembayaran }}"
                                class="w-full rounded-xl border border-gray-200 object-cover max-h-48 hover:opacity-90 transition-opacity"
                                alt="Bukti Pembayaran">
                        </a>
                    @elseif($ext === 'pdf')
                        <a href="/{{ $pembayaran->bukti_pembayaran }}" target="_blank"
                            class="inline-flex items-center gap-2 text-sm text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-xl border border-red-200 transition-colors w-full justify-center">
                            <i class="fa fa-file-pdf"></i> Lihat PDF
                        </a>
                    @else
                        <a href="/{{ $pembayaran->bukti_pembayaran }}" target="_blank"
                            class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl border border-blue-200 transition-colors w-full justify-center">
                            <i class="fa fa-file"></i> Download File
                        </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Metadata --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4 space-y-2">
                <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide">Metadata</p>
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Dibuat</span>
                        <span class="text-gray-600">{{ $pembayaran->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Diupdate</span>
                        <span class="text-gray-600">{{ $pembayaran->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">ID</span>
                        <span class="text-gray-600 font-mono">#{{ $pembayaran->id }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Approval Modal (untuk aksi approve/reject dari halaman ini) --}}
@if($role === 'superadmin')
<x-approval-modal />

<script>
// Re-use fungsi yang ada di index untuk approve/reject
function openSingleApproveModal(id, noPr) {
    const modal = document.getElementById('approveModal');
    if (modal) {
        document.getElementById('approvePembayaranId').value = id;
        const title = modal.querySelector('[data-title]') || modal.querySelector('h3');
        if (title) title.textContent = 'Setujui PR ' + noPr;
        modal.classList.remove('hidden');
    }
}
function openSingleRejectModal(id, noPr) {
    const modal = document.getElementById('rejectModal');
    if (modal) {
        document.getElementById('rejectPembayaranId').value = id;
        const title = modal.querySelector('[data-title]') || modal.querySelector('h3');
        if (title) title.textContent = 'Tolak PR ' + noPr;
        modal.classList.remove('hidden');
    }
}
</script>
@endif
@endsection
