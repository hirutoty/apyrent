@extends('admin.layouts.app')

@section('title', 'Detail Rental #' . $rental->id)

@push('styles')
    <style>
        .badge-Pending {
            background: #6B7280;
            color: #fff;
        }

        .badge-booking {
            background: #3B82F6;
            color: #fff;
        }

        .badge-aktif {
            background: #10B981;
            color: #fff;
        }

        .badge-selesai {
            background: #1F2937;
            color: #fff;
        }

        .badge-batal {
            background: #EF4444;
            color: #fff;
        }

        .stat-card {
            transition: transform .15s, box-shadow .15s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .timeline-step {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            left: .45rem;
            top: 1.6rem;
            width: 2px;
            height: calc(100% - .8rem);
            background: #E5E7EB;
        }

        .timeline-step:last-child::before {
            display: none;
        }

        .section-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #F1F5F9;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            overflow: hidden;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #F1F5F9;
        }

        .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6 pb-8">

        {{-- ── PAGE HEADER ─────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('rental.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors text-sm">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Rental</h1>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-xs font-bold
                    @if ($rental->status == 'Pending') badge-Pending
                    @elseif($rental->status == 'booking') badge-booking
                    @elseif($rental->status == 'aktif') badge-aktif
                    @elseif($rental->status == 'selesai') badge-selesai
                    @else badge-batal @endif">
                        {{ strtoupper($rental->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-400 ml-6">
                    ID #{{ $rental->id }} &bull;
                    Dibuat {{ \Carbon\Carbon::parse($rental->created_at)->locale('id')->diffForHumans() }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('rental.invoice', $rental->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 text-sm font-medium text-red-600 border border-red-200 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-colors">
                    <i class="fa fa-file-pdf"></i> Invoice PDF
                </a>
                <a href="{{ route('rental.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 border border-gray-200 px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- ── STAT MINI CARDS ─────────────────────────────────── --}}

        @php
            $sudahBayar = ($rental->nominal_dp ?? 0) + ($rental->nominal_pelunasan ?? 0);
            if ($rental->jenis_pembayaran == 'lunas' && $rental->bukti_lunas) {
                $sudahBayar = $rental->total_biaya;
            }
            $sisa = max(0, $rental->total_biaya - $sudahBayar);
        @endphp


        {{-- ── MAIN GRID ──────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ════════════════ LEFT COLUMN ════════════════ --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- DATA RENTAL --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="flex items-center gap-3">
                            <div class="section-icon bg-blue-100 text-blue-600">
                                <i class="fa fa-car"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 text-sm">Data Rental</h2>
                                <p class="text-xs text-gray-400">Informasi transaksi lengkap</p>
                            </div>
                        </div>
                        <span
                            class="text-xs text-gray-400 font-mono bg-gray-100 px-2 py-1 rounded-lg">#{{ $rental->id }}</span>
                    </div>

                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                                <i class="fa fa-user mr-1 text-blue-400"></i> Member
                            </p>
                            <p class="text-sm font-bold text-gray-800">{{ $rental->member->nama_member ?? '-' }}</p>
                            @if ($rental->member->kontak_member ?? false)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <i class="fa fa-phone mr-1"></i>{{ $rental->member->kontak_member }}
                                </p>
                            @endif
                            @if ($rental->member->alamat ?? false)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <i class="fa fa-map-marker-alt mr-1"></i>{{ $rental->member->alamat }}
                                </p>
                            @endif
                        </div>

                        <div class="bg-blue-50 rounded-xl p-3.5">
                            <p class="text-xs font-semibold text-blue-400 uppercase tracking-wide mb-2">
                                <i class="fa fa-car mr-1"></i> Kendaraan
                            </p>
                            <p class="text-sm font-bold text-gray-800">{{ $rental->kendaraan->merk ?? '-' }}</p>
                            <p class="text-xs font-mono text-blue-600 bg-blue-100 inline-block px-2 py-0.5 rounded-lg mt-1">
                                {{ $rental->kendaraan->nopol ?? '-' }}
                            </p>

                        </div>

                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                                <i class="fa fa-user-shield mr-1 text-gray-500"></i> Dibuat Oleh
                            </p>
                            <p class="text-sm font-bold text-gray-800">{{ $rental->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $rental->user->email ?? '' }}</p>
                        </div>

                        <div class="bg-green-50 rounded-xl p-3.5">
                            <p class="text-xs font-semibold text-green-500 uppercase tracking-wide mb-2">
                                <i class="fa fa-calendar-check mr-1"></i> Mulai
                            </p>
                            <p class="text-sm font-bold text-gray-800">{{ $rental->tanggal_mulai }}</p>
                        </div>

                        <div class="bg-red-50 rounded-xl p-3.5">
                            <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-2">
                                <i class="fa fa-calendar-times mr-1"></i> Selesai
                            </p>
                            <p class="text-sm font-bold text-gray-800">{{ $rental->tanggal_selesai }}</p>
                        </div>

                        <div class="bg-purple-50 rounded-xl p-3.5">
                            <p class="text-xs font-semibold text-purple-400 uppercase tracking-wide mb-2">
                                <i class="fa fa-clock mr-1"></i> Durasi
                            </p>
                            <p class="text-sm font-bold text-gray-800">
                                @if ($rental->durasi_bulan)
                                    {{ $rental->durasi_bulan }} Bulan
                                @elseif($rental->durasi_hari)
                                    {{ $rental->durasi_hari }} Hari
                                @else
                                    -
                                @endif
                            </p>
                            <p class="text-xs text-purple-500 mt-0.5">
                                {{ strtoupper($rental->metode_pembayaran ?? '-') }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- DATA DRIVER & TUJUAN --}}
@if($rental->tujuan || $rental->nama_driver || $rental->kontak_driver || $rental->biaya_driver)
<div class="section-card">
    <div class="section-header">
        <div class="flex items-center gap-3">
            <div class="section-icon bg-amber-100 text-amber-600">
                <i class="fa fa-car-side"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 text-sm">Perjalanan & Driver</h2>
                <p class="text-xs text-gray-400">Info tujuan dan driver harian</p>
            </div>
        </div>
        <span class="text-xs font-medium px-2 py-1 rounded-full bg-amber-100 text-amber-700">Per Hari</span>
    </div>

    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Tujuan --}}
        <div class="sm:col-span-2 bg-amber-50 rounded-xl p-3.5">
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-wide mb-1.5">
                <i class="fa fa-map-marker-alt mr-1"></i> Tujuan Perjalanan
            </p>
            <p class="text-sm font-bold text-gray-800">{{ $rental->tujuan ?? '-' }}</p>
        </div>

        {{-- Nama Driver --}}
        <div class="bg-gray-50 rounded-xl p-3.5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                <i class="fa fa-user mr-1 text-gray-400"></i> Nama Driver
            </p>
            <p class="text-sm font-bold text-gray-800">{{ $rental->nama_driver ?? 'No Driver' }}</p>
        </div>

        {{-- Kontak Driver --}}
        <div class="bg-gray-50 rounded-xl p-3.5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                <i class="fa fa-phone mr-1 text-gray-400"></i> Kontak Driver
            </p>
            <p class="text-sm font-bold text-gray-800">{{ $rental->kontak_driver ?? 'No Driver' }}</p>
        </div>

        {{-- Biaya Driver --}}
        
        <div class="sm:col-span-2 bg-blue-50 rounded-xl p-3.5">
            <p class="text-xs font-semibold text-blue-400 uppercase tracking-wide mb-1.5">
                <i class="fa fa-money-bill-wave mr-1"></i> Biaya Driver
            </p>
            <p class="text-sm font-bold text-blue-700">
                Rp {{ number_format($rental->biaya_driver) }}
            </p>
        </div>
        

    </div>
</div>
@endif  

                {{-- TIMELINE STATUS --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="flex items-center gap-3">
                            <div class="section-icon bg-purple-100 text-purple-600">
                                <i class="fa fa-history"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 text-sm">Alur Status Rental</h2>
                                <p class="text-xs text-gray-400">Progres tahapan transaksi</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-5">
                        @php
                            $steps = [
                                [
                                    'key' => 'Pending',
                                    'label' => 'Pending',
                                    'desc' => 'Rental baru masuk, menunggu pembayaran',
                                    'color' => 'gray',
                                    'icon' => 'fa-hourglass-half',
                                ],
                                [
                                    'key' => 'booking',
                                    'label' => 'Booking',
                                    'desc' => 'Pembayaran dikonfirmasi, kendaraan dipesan',
                                    'color' => 'blue',
                                    'icon' => 'fa-calendar-check',
                                ],
                                [
                                    'key' => 'aktif',
                                    'label' => 'Aktif',
                                    'desc' => 'Kendaraan sedang digunakan',
                                    'color' => 'green',
                                    'icon' => 'fa-car',
                                ],
                                [
                                    'key' => 'selesai',
                                    'label' => 'Selesai',
                                    'desc' => 'Rental telah selesai',
                                    'color' => 'purple',
                                    'icon' => 'fa-flag-checkered',
                                ],
                            ];
                            $order = ['Pending' => 0, 'booking' => 1, 'aktif' => 2, 'selesai' => 3, 'batal' => -1];
                            $currentIdx = $order[$rental->status] ?? -1;

                            $colorMap = [
                                'gray' => [
                                    'bg' => 'bg-gray-100',
                                    'text' => 'text-gray-500',
                                    'dot' => 'bg-gray-400',
                                    'ring' => 'ring-gray-200',
                                ],
                                'blue' => [
                                    'bg' => 'bg-blue-100',
                                    'text' => 'text-blue-600',
                                    'dot' => 'bg-blue-500',
                                    'ring' => 'ring-blue-200',
                                ],
                                'green' => [
                                    'bg' => 'bg-green-100',
                                    'text' => 'text-green-600',
                                    'dot' => 'bg-green-500',
                                    'ring' => 'ring-green-200',
                                ],
                                'purple' => [
                                    'bg' => 'bg-purple-100',
                                    'text' => 'text-purple-600',
                                    'dot' => 'bg-purple-500',
                                    'ring' => 'ring-purple-200',
                                ],
                            ];
                        @endphp

                        @if ($rental->status == 'batal')
                            <div
                                class="flex items-center gap-3 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl">
                                <i class="fa fa-times-circle text-lg"></i>
                                <div>
                                    <p class="text-sm font-bold">Rental Dibatalkan</p>
                                    <p class="text-xs text-red-400 mt-0.5">Transaksi ini telah dibatalkan dan tidak dapat
                                        dilanjutkan.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-0 overflow-x-auto pb-2">
                                @foreach ($steps as $idx => $step)
                                    @php
                                        $c = $colorMap[$step['color']];
                                        $done = $idx < $currentIdx;
                                        $active = $idx == $currentIdx;
                                        $future = $idx > $currentIdx;
                                    @endphp
                                    <div class="flex-1 min-w-[120px] flex flex-col items-center text-center relative">
                                        {{-- connector line left --}}
                                        @if ($idx > 0)
                                            <div
                                                class="absolute top-4 left-0 w-1/2 h-0.5
                                        {{ $done || $active ? 'bg-green-300' : 'bg-gray-200' }}">
                                            </div>
                                        @endif
                                        {{-- connector line right --}}
                                        @if ($idx < count($steps) - 1)
                                            <div
                                                class="absolute top-4 right-0 w-1/2 h-0.5
                                        {{ $done ? 'bg-green-300' : 'bg-gray-200' }}">
                                            </div>
                                        @endif

                                        {{-- dot --}}
                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center mb-2
                                    {{ $done ? 'bg-green-500' : ($active ? $c['bg'] . ' ring-2 ' . $c['ring'] : 'bg-gray-100') }}">
                                            @if ($done)
                                                <i class="fa fa-check text-white text-xs"></i>
                                            @else
                                                <i
                                                    class="fa {{ $step['icon'] }} text-xs {{ $active ? $c['text'] : 'text-gray-300' }}"></i>
                                            @endif
                                        </div>

                                        <p
                                            class="text-xs font-bold {{ $done ? 'text-green-600' : ($active ? $c['text'] : 'text-gray-300') }}">
                                            {{ $step['label'] }}
                                        </p>
                                        <p
                                            class="text-xs {{ $active ? 'text-gray-500' : 'text-gray-300' }} mt-0.5 px-1 leading-tight hidden sm:block">
                                            {{ $step['desc'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ════════════════ RIGHT COLUMN ════════════════ --}}
            <div class="space-y-6">

                {{-- RINGKASAN BIAYA --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="flex items-center gap-3">
                            <div class="section-icon bg-green-100 text-green-600">
                                <i class="fa fa-wallet"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 text-sm">Ringkasan Biaya</h2>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-100">
                            <span class="text-sm text-gray-500">Biaya Kendaraan</span>
                            <span class="text-sm font-semibold text-gray-800">Rp
                                {{ number_format($rental->biaya_dasar) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-100">
                            <span class="text-sm text-gray-500">Biaya Tambahan</span>
                            <span
                                class="text-sm font-semibold {{ $rental->biaya_tambahan_total > 0 ? 'text-orange-600' : 'text-gray-800' }}">
                                Rp {{ number_format($rental->biaya_tambahan_total) }}
                            </span>
                        </div>
                        @if ($rental->nominal_dp)
                            <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-100">
                                <span class="text-sm text-gray-500">DP Terbayar</span>
                                <span class="text-sm font-semibold text-blue-600">- Rp
                                    {{ number_format($rental->nominal_dp) }}</span>
                            </div>
                        @endif
                        <div
                            class="pt-2 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 px-4 py-3 flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Total</span>
                            <span class="text-2xl font-black text-green-600">Rp
                                {{ number_format($rental->total_biaya) }}</span>
                        </div>
                        @if ($sisa > 0)
                            <div class="flex justify-between items-center bg-red-50 px-3 py-2.5 rounded-xl">
                                <span class="text-xs font-semibold text-red-500">Sisa Tagihan</span>
                                <span class="text-sm font-bold text-red-600">Rp {{ number_format($sisa) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- BUKTI PEMBAYARAN --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="flex items-center gap-3">
                            <div class="section-icon bg-indigo-100 text-indigo-600">
                                <i class="fa fa-file-invoice"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800 text-sm">Bukti Pembayaran</h2>
                                <p class="text-xs text-gray-400">
                                    {{ strtoupper($rental->jenis_pembayaran ?? '-') }} &bull;
                                    {{ strtoupper($rental->metode_pembayaran ?? '-') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-4 space-y-4">

                        @if ($rental->jenis_pembayaran == 'lunas')

                            @if ($rental->bukti_lunas)
                                <div>
                                    <p class="text-xs font-bold text-green-600 mb-2 flex items-center gap-1">
                                        <i class="fa fa-check-circle"></i> Bukti Lunas
                                    </p>
                                    @if ($rental->bukti_lunas)
                                        <a href="{{ asset($rental->bukti_lunas) }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            {{ basename($rental->bukti_lunas) }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">Belum ada bukti lunas</span>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1 text-center">Klik untuk perbesar</p>
                                </div>
                            @else
                                <div
                                    class="flex flex-col items-center gap-2 py-4 bg-yellow-50 rounded-xl border border-yellow-100">
                                    <i class="fa fa-exclamation-triangle text-yellow-400 text-xl"></i>
                                    <p class="text-xs text-yellow-600 font-semibold">Belum upload bukti lunas</p>
                                </div>
                                <form action="{{ route('rental.uploadBuktiTf', $rental->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="jenis_pembayaran" value="lunas">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Upload Bukti
                                            Lunas</label>
                                        <input type="file" name="bukti_lunas" required
                                            accept="image/*,application/pdf"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                                        <i class="fa fa-upload text-xs"></i> Upload Bukti Lunas
                                    </button>
                                </form>
                            @endif
                        @elseif($rental->jenis_pembayaran == 'dp')
                            {{-- Bukti DP --}}
                            <div>
                                <p class="text-xs font-bold text-blue-600 mb-2 flex items-center gap-1">
                                    <i class="fa fa-credit-card"></i> Bukti DP
                                    @if ($rental->nominal_dp)
                                        <span class="ml-auto text-blue-500 font-normal">Rp
                                            {{ number_format($rental->nominal_dp) }}</span>
                                    @endif
                                </p>
                                @if ($rental->bukti_dp)
                                    <a href="{{ asset($rental->bukti_dp) }}" target="_blank"
                                        class="text-blue-600 hover:underline">
                                        {{ basename($rental->bukti_dp) }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Belum ada bukti lunas</span>
                                @endif
                            @else
                                <div
                                    class="flex flex-col items-center gap-2 py-4 bg-yellow-50 rounded-xl border border-yellow-100 mb-3">
                                    <i class="fa fa-exclamation-triangle text-yellow-400 text-xl"></i>
                                    <p class="text-xs text-yellow-600 font-semibold">Belum upload bukti DP</p>
                                </div>
                                <form action="{{ route('rental.uploadBuktiTf', $rental->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="jenis_pembayaran" value="dp">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nominal
                                            DP</label>
                                        <input type="number" name="nominal_dp" required placeholder="Nominal DP"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Upload Bukti
                                            DP</label>
                                        <input type="file" name="bukti_dp" required
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                                        <i class="fa fa-upload text-xs"></i> Upload Bukti DP
                                    </button>
                                </form>
                        @endif
                    </div>

                    {{-- Bukti Pelunasan --}}
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-bold text-green-600 mb-2 flex items-center gap-1">
                            <i class="fa fa-check-circle"></i> Bukti Pelunasan
                            @if ($rental->nominal_pelunasan)
                                <span class="ml-auto text-green-500 font-normal">Rp
                                    {{ number_format($rental->nominal_pelunasan) }}</span>
                            @endif
                        </p>
                        @if ($rental->bukti_pelunasan)
                            <a href="{{ asset($rental->bukti_pelunasan) }}" target="_blank"
                                class="text-blue-600 hover:underline">
                                {{ basename($rental->bukti_pelunasan) }}
                            </a>
                        @else
                            <span class="text-gray-400">Belum ada bukti lunas</span>
                        @endif

                        @if ($rental->status == 'aktif')
                            @php $sisaPelunasan = $rental->total_biaya - ($rental->nominal_dp ?? 0); @endphp
                            <div class="bg-blue-50 rounded-xl px-3 py-2 mb-3 flex justify-between items-center">
                                <span class="text-xs text-blue-500">Sisa Pelunasan</span>
                                <span class="text-sm font-bold text-blue-700">Rp
                                    {{ number_format($sisaPelunasan) }}</span>
                            </div>
                            @if (empty($rental->bukti_pelunasan))
                                <form action="{{ route('rental.pelunasan', $rental->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-3">
                                    @csrf

                                    <input type="hidden" name="nominal_pelunasan" value="{{ $sisaPelunasan }}">

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                                            Upload Bukti Pelunasan
                                        </label>

                                        <input type="file" name="bukti_pelunasan" required
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                    </div>

                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                                        <i class="fa fa-upload text-xs"></i>
                                        Upload Pelunasan
                                    </button>
                                </form>
                            @else
                               
                            @endif
                        @else
                            <div class="flex flex-col items-center gap-2 py-4 bg-gray-50 rounded-xl">
                                <i class="fa fa-image text-gray-300 text-xl"></i>
                                <p class="text-xs text-gray-400">Belum upload bukti pelunasan</p>
                            </div>
                        @endif

                    </div>



                </div>
            </div>

            {{-- UPDATE STATUS --}}
            <div class="section-card">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="section-icon bg-gray-100 text-gray-600">
                            <i class="fa fa-exchange-alt"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-800 text-sm">Update Status</h2>
                            <p class="text-xs text-gray-400">Kelola tahapan rental</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-3">

                    @if ($rental->status == 'selesai')
                        <div
                            class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                            <i class="fa fa-check-circle text-green-500 text-lg"></i>
                            <p class="text-sm font-semibold">Rental sudah selesai</p>
                        </div>
                    @elseif($rental->status == 'batal')
                        <div
                            class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
                            <i class="fa fa-times-circle text-red-500 text-lg"></i>
                            <p class="text-sm font-semibold">Rental telah dibatalkan</p>
                        </div>
                    @else
                        {{-- Pending → BOOKING --}}
                        @if ($rental->status == 'Pending')
                            @php
                                $sudahBayar =
                                    ($rental->jenis_pembayaran == 'lunas' && $rental->bukti_lunas) ||
                                    ($rental->jenis_pembayaran == 'dp' && $rental->bukti_dp);
                            @endphp
                            @if ($sudahBayar)
                                <form action="{{ route('rental.updateStatus', $rental->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="booking">
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                                        <i class="bi bi-calendar-check"></i> Konfirmasi Booking
                                    </button>
                                </form>
                            @else
                                <div
                                    class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl">
                                    <i class="fa fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                                    <p class="text-xs">Upload bukti pembayaran terlebih dahulu sebelum konfirmasi
                                        booking.</p>
                                </div>
                            @endif
                        @endif

                        {{-- BOOKING → AKTIF --}}
                        @if ($rental->status == 'booking')
                            <form action="{{ route('rental.updateStatus', $rental->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                                    <i class="bi bi-check-circle"></i> Aktifkan Rental
                                </button>
                            </form>
                        @endif

                        {{-- AKTIF → SELESAI --}}
                        @if ($rental->status == 'aktif')
                            @if ($rental->jenis_pembayaran == 'dp' && !$rental->bukti_pelunasan)
                                <div
                                    class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl">
                                    <i class="fa fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                                    <p class="text-xs">Upload bukti pelunasan terlebih dahulu sebelum menyelesaikan
                                        rental.</p>
                                </div>
                            @else
                                <form action="{{ route('rental.updateStatus', $rental->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                                        <i class="bi bi-flag"></i> Selesaikan Rental
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- BATALKAN --}}
                        @if (in_array($rental->status, ['Pending', 'booking', 'aktif']))
                            <form action="{{ route('rental.updateStatus', $rental->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin membatalkan rental ini?')">
                                @csrf
                                <input type="hidden" name="status" value="batal">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-bold py-2.5 rounded-xl transition-colors border border-red-200">
                                    <i class="bi bi-x-circle"></i> Batalkan Rental
                                </button>
                            </form>
                        @endif

                    @endif
                </div>
            </div>

        </div>
    </div>

    </div>

    {{-- ── POPUP ALERT ──────────────────────────────────────── --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity .2s;pointer-events:none">
            <div id="alertBox"
                class="bg-white rounded-2xl shadow-2xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform .25s">
                @if (session('success'))
                    <div
                        class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        @if (session('error'))
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        @else
                            <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
                <button onclick="closeAlert()"
                    class="text-gray-300 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <script>
        (function() {
            var o = document.getElementById('alertOverlay');
            var b = document.getElementById('alertBox');
            if (!o) return;
            setTimeout(function() {
                o.style.opacity = '1';
                o.style.pointerEvents = 'auto';
                b.style.transform = 'translateY(0)';
            }, 80);
            var t = setTimeout(closeAlert, 4500);
            o.addEventListener('click', function(e) {
                if (e.target === o) closeAlert();
            });

            function closeAlert() {
                clearTimeout(t);
                o.style.opacity = '0';
                o.style.pointerEvents = 'none';
                b.style.transform = 'translateY(-16px)';
            }
            window.closeAlert = closeAlert;
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const totalBiaya = Number({{ $rental->total_biaya ?? 0 }});
            const dp = Number({{ $rental->nominal_dp ?? 0 }});
            const pelunasanInput = document.getElementById('nominal_pelunasan');
            if (pelunasanInput) {
                const sisa = totalBiaya - dp;
                pelunasanInput.value = (sisa > 0 ? sisa : 0);
            }
        });
    </script>
@endsection
