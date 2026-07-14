@extends('admin.layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Member</h1>
            <p class="text-sm text-gray-500 mt-0.5">Informasi lengkap mitra pemilik kendaraan</p>
        </div>
        <a href="{{ route('members.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">
            <i class="fa fa-arrow-left text-sm"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- -- KARTU PROFIL MEMBER -- --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Info Utama --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col items-center text-center mb-5">
                    <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold mb-3">
                        {{ strtoupper(substr($member->nama, 0, 2)) }}
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $member->nama }}</h2>
                    <span class="mt-1 px-3 py-0.5 text-xs rounded-full font-medium
                        {{ $member->jenis_member == 'perorangan' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                        {{ ucfirst($member->jenis_member) }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-phone text-gray-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Kontak</p>
                            <p class="font-medium text-gray-700">{{ $member->kontak ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-envelope text-gray-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="font-medium text-gray-700">{{ $member->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-map-marker-alt text-gray-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Alamat</p>
                            <p class="font-medium text-gray-700">{{ $member->alamat ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-car text-indigo-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Total Kendaraan</p>
                            <p class="font-bold text-indigo-600 text-lg">{{ $member->kendaraans->count() }} Unit</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-calendar text-gray-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Bergabung</p>
                            <p class="font-medium text-gray-700">{{ $member->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dokumen --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fa fa-folder-open text-blue-400"></i> Dokumen
                </h3>
                <div class="space-y-2.5">

                    @php
                        $dokumens = [
                            ['field' => 'file_stnk',       'label' => 'STNK',       'icon' => 'fa-id-card',      'color' => 'amber'],
                            ['field' => 'file_attachment',  'label' => 'Attachment', 'icon' => 'fa-paperclip',    'color' => 'purple'],
                            ['field' => 'file_kontrak',     'label' => 'Kontrak',    'icon' => 'fa-file-contract','color' => 'teal'],
                        ];
                    @endphp

                    @foreach($dokumens as $dok)
                        @php $val = $member->{$dok['field']}; $c = $dok['color']; @endphp

                        @if($dok['field'] === 'file_stnk')
                            @php $hasStnk = !empty($val) && is_array($val); @endphp
                            <div class="p-3 rounded-xl border {{ $hasStnk ? 'bg-amber-50 border-amber-200' : 'bg-gray-50 border-gray-200' }}">
                                <div class="flex items-center gap-2.5 mb-2">
                                    <div class="w-8 h-8 rounded-lg {{ $hasStnk ? 'bg-amber-100 text-amber-600' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center">
                                        <i class="fa fa-id-card text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $hasStnk ? 'text-gray-700' : 'text-gray-400' }}">STNK</p>
                                        <p class="text-xs {{ $hasStnk ? 'text-amber-600' : 'text-gray-400' }}">
                                            {{ $hasStnk ? count($val).' file tersedia' : 'Belum diupload' }}
                                        </p>
                                    </div>
                                </div>
                                @if($hasStnk)
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @foreach($val as $idx => $stnkPath)
                                            <a href="{{ asset($stnkPath) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium bg-white border border-amber-300 text-amber-700 rounded-lg hover:bg-amber-50 transition-colors">
                                                <i class="fa fa-download text-xs"></i> STNK {{ count($val) > 1 ? $idx + 1 : '' }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        @elseif($dok['field'] === 'file_attachment')
                            @php $hasAtt = !empty($val) && is_array($val); @endphp
                            <div class="p-3 rounded-xl border {{ $hasAtt ? 'bg-purple-50 border-purple-200' : 'bg-gray-50 border-gray-200' }}">
                                <div class="flex items-center gap-2.5 mb-2">
                                    <div class="w-8 h-8 rounded-lg {{ $hasAtt ? 'bg-purple-100 text-purple-600' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center">
                                        <i class="fa fa-paperclip text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $hasAtt ? 'text-gray-700' : 'text-gray-400' }}">Attachment</p>
                                        <p class="text-xs {{ $hasAtt ? 'text-purple-600' : 'text-gray-400' }}">
                                            {{ $hasAtt ? count($val).' file tersedia' : 'Belum diupload' }}
                                        </p>
                                    </div>
                                </div>
                                @if($hasAtt)
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @foreach($val as $idx => $attPath)
                                            <a href="{{ asset($attPath) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium bg-white border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors">
                                                <i class="fa fa-download text-xs"></i> File {{ $idx + 1 }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        @else
                            <div class="flex items-center justify-between p-3 rounded-xl border
                                {{ $val ? 'bg-'.$c.'-50 border-'.$c.'-200' : 'bg-gray-50 border-gray-200' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg {{ $val ? 'bg-'.$c.'-100 text-'.$c.'-600' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center">
                                        <i class="fa {{ $dok['icon'] }} text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold {{ $val ? 'text-gray-700' : 'text-gray-400' }}">{{ $dok['label'] }}</p>
                                        <p class="text-xs {{ $val ? 'text-'.$c.'-600' : 'text-gray-400' }}">
                                            {{ $val ? 'Tersedia' : 'Belum diupload' }}
                                        </p>
                                    </div>
                                </div>
                                @if($val)
                                    <a href="{{ asset($val) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium bg-white border border-{{ $c }}-300 text-{{ $c }}-700 rounded-lg hover:bg-{{ $c }}-50 transition-colors">
                                        <i class="fa fa-download text-xs"></i> Unduh
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endforeach

                </div>
            </div>

        </div>

        {{-- -- DAFTAR KENDARAAN -- --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">Kendaraan Milik Member</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $member->kendaraans->count() }} unit terdaftar</p>
                    </div>
                    <a href="{{ route('kendaraan.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fa fa-plus text-xs"></i> Tambah Kendaraan
                    </a>
                </div>

                @if($member->kendaraans->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fa fa-car text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Belum ada kendaraan terhubung</p>
                            <p class="text-xs text-gray-400">Tambah kendaraan dan pilih member ini sebagai pemilik</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kendaraan</th>
                                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nopol</th>
                                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Jenis</th>
                                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Warna</th>
                                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Harga/Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($member->kendaraans as $k)
                                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                @if($k->foto)
                                                    <img src="{{ asset($k->foto) }}" alt=""
                                                        class="w-10 h-10 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                                                @else
                                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa fa-car text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-semibold text-gray-800 text-sm">{{ $k->merk }}</p>
                                                    <p class="text-xs text-gray-400">{{ $k->tahun_pembuatan ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $k->nopol }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $k->jenis->nama ?? '-' }}</td>
                                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $k->warna ?? '-' }}</td>
                                        <td class="px-4 py-3.5">
                                            @php
                                                $statusColor = match($k->status_kendaraan) {
                                                    'tersedia'    => 'bg-green-100 text-green-700',
                                                    'disewa'      => 'bg-blue-100 text-blue-700',
                                                    'service'     => 'bg-amber-100 text-amber-700',
                                                    'bermasalah'  => 'bg-red-100 text-red-700',
                                                    default       => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColor }}">
                                                {{ ucfirst($k->status_kendaraan ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-xs font-semibold text-gray-700">
                                            Rp {{ number_format($k->harga_sewa_per_hari ?? 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>
@endsection
