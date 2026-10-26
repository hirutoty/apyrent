@extends('admin.layouts.app')

@section('title', 'Detail Ceklist Kondisi')

@section('content')

<div class="space-y-6">

    {{-- BACK BUTTON & HEADER --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('ceklist-kondisi.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Ceklist Kondisi</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $ceklist->tanggal->format('d F Y') }}</p>
            </div>
        </div>
        <a href="{{ route('ceklist-kondisi.print', $ceklist->id) }}" 
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl shadow-sm transition-colors">
            <i class="bi bi-printer-fill"></i> Print (F4)
        </a>
    </div>

    {{-- INFO KENDARAAN --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-500 text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $ceklist->kendaraan->nopol }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $ceklist->kendaraan->merk }}</p>
            </div>
        </div>
    </div>

    {{-- INFO UMUM --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="bi bi-info-circle-fill text-blue-500 mr-2"></i>Informasi Umum
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Nama</p>
                <p class="text-base font-semibold text-gray-800">{{ $ceklist->nama }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Perusahaan</p>
                <p class="text-base font-semibold text-gray-800">{{ $ceklist->perusahaan }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">No. Telephone</p>
                <p class="text-base font-semibold text-gray-800">{{ $ceklist->no_telephone }}</p>
            </div>
            <div class="md:col-span-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Alamat</p>
                <p class="text-sm text-gray-700">{{ $ceklist->alamat }}</p>
            </div>
        </div>
    </div>

    {{-- INFO PEMERIKSAAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="bi bi-calendar-check-fill text-green-500 mr-2"></i>Informasi Pemeriksaan
            </h3>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Tanggal</p>
                <p class="text-sm font-semibold text-gray-800">{{ $ceklist->tanggal->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Kilometer</p>
                <p class="text-sm font-semibold text-blue-600">{{ number_format($ceklist->kilometer) }} km</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Jam</p>
                <p class="text-sm font-semibold text-gray-800">{{ $ceklist->jam }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Fuel</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                    {{ $ceklist->fuel === 'F' ? 'bg-green-100 text-green-700' : ($ceklist->fuel === 'E' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $ceklist->fuel }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Pemeriksa</p>
                <p class="text-sm font-semibold text-gray-800">{{ $ceklist->pemeriksa }}</p>
            </div>
            @if($ceklist->status)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-700">
                    {{ ucfirst($ceklist->status) }}
                </span>
            </div>
            @endif
            @if($ceklist->pemakaian_bbm)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Pemakaian BBM</p>
                <p class="text-sm font-semibold text-gray-800">{{ $ceklist->pemakaian_bbm }}</p>
            </div>
            @endif
            @if($ceklist->ratio)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Ratio</p>
                <p class="text-sm font-semibold text-gray-800">{{ $ceklist->ratio }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- FOTO KENDARAAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="bi bi-camera-fill text-purple-500 mr-2"></i>Foto Kendaraan
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(['depan', 'atas', 'belakang', 'sampingkiri', 'sampingkanan'] as $posisi)
                    @php $fotoField = 'foto_' . $posisi; $ketField = 'ket_' . $posisi; @endphp
                    @if($ceklist->$fotoField)
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-3 border-b border-gray-200">
                            <p class="text-sm font-bold text-gray-700 uppercase">{{ str_replace('samping', 'Samping ', ucfirst($posisi)) }}</p>
                        </div>
                        <a href="{{ asset('storage/' . $ceklist->$fotoField) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/' . $ceklist->$fotoField) }}" 
                                alt="Foto {{ $posisi }}" 
                                class="w-full h-64 object-cover hover:opacity-90 transition-opacity cursor-pointer">
                        </a>
                        @if($ceklist->$ketField)
                        <div class="p-3 bg-gray-50 border-t border-gray-200">
                            <p class="text-xs text-gray-600">{{ $ceklist->$ketField }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- KONDISI KENDARAAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="bi bi-clipboard-check-fill text-orange-500 mr-2"></i>Kondisi Kendaraan
            </h3>
        </div>
        <div class="p-6">
            @php
                $kondisiFields = [
                    'Mesin' => 'kondisi_mesin', 'Rem' => 'kondisi_rem', 'Kopling' => 'kondisi_kopling', 
                    'Ban' => 'kondisi_ban', 'AC' => 'kondisi_ac', 'Lampu Lampu Sen' => 'lampu_lampusen',
                    'Lampu Besar/Kecil' => 'lampu_lampubesarkecil', 'Lampu Rem/Bik/Plat' => 'lampu_rembikplat',
                    'Accu' => 'accu', 'Air Radiator' => 'airradiator', 'Oli Mesin/Rem/Kopling' => 'olimesin_remkopling',
                    'Oli Power Steering' => 'olipower_steering', 'Kebersihan Interior' => 'kebersihan_interior',
                    'Kerapihan Body/Cat' => 'kerapihanbody_cat', 'Perlengkapan' => 'perlengkapan',
                    'Ban Cadang' => 'ban_cadang', 'Dongkrak' => 'dongkrak', 'Kunci Roda' => 'kunciroda',
                    'Radio/Tape' => 'radio_tape', 'Merk' => 'merk', 'Karpet Lantai' => 'karpet_lantai',
                    'Dop Roda' => 'doproda', 'Pematik Api' => 'pematikapi', 'Segitiga Pengaman' => 'segitiga_pengaman',
                    'Kotak Obat' => 'kotakobat', 'STNK' => 'stnk', 'Kunci Pintu' => 'kuncipintu',
                    'Speedometer' => 'spedoometer'
                ];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($kondisiFields as $label => $field)
                    @php
                        $nilai = $ceklist->$field;
                        $isBaik = ($nilai === 'B');
                        $isRusak = ($nilai === 'R');
                        $isKosong = (!$nilai || !in_array($nilai, ['B', 'R']));
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg border 
                        {{ $isBaik ? 'bg-green-50 border-green-200' : ($isRusak ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        @if($isBaik)
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-green-500 text-white text-sm font-bold">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        @elseif($isRusak)
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-red-500 text-white text-sm font-bold">
                                <i class="bi bi-x-lg"></i>
                            </span>
                        @else
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-gray-200 border border-gray-300 text-gray-400 text-xs">
                                <i class="bi bi-dash"></i>
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ACCECORIS TAMBAHAN --}}
    @if($ceklist->accecoris_tambahan)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="bi bi-plus-circle-fill text-indigo-500 mr-2"></i>Accesoris Tambahan
            </h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-700 leading-relaxed">{{ $ceklist->accecoris_tambahan }}</p>
        </div>
    </div>
    @endif

    {{-- PENANGGUNG JAWAB & TANDA TANGAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="bi bi-people-fill text-cyan-500 mr-2"></i>Penanggung Jawab & Tanda Tangan
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- SOPIR --}}
                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Sopir</p>
                    @if($ceklist->sopir)
                        <p class="text-lg font-bold text-gray-800 mb-4">{{ $ceklist->sopir }}</p>
                    @else
                        <p class="text-sm text-gray-400 mb-4">-</p>
                    @endif
                    
                    @if($ceklist->ttd_supir)
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Tanda Tangan:</p>
                            <div class="border-2 border-cyan-200 rounded-lg overflow-hidden bg-white">
                                <a href="{{ asset('storage/' . $ceklist->ttd_supir) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $ceklist->ttd_supir) }}" 
                                        alt="TTD Sopir" 
                                        class="w-full h-32 object-contain p-2 hover:opacity-80 transition-opacity cursor-pointer">
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-3 p-3 bg-gray-100 rounded-lg text-center">
                            <i class="bi bi-pen text-gray-400 text-2xl"></i>
                            <p class="text-xs text-gray-400 mt-1">Tidak ada tanda tangan</p>
                        </div>
                    @endif
                </div>

                {{-- MEKANIK --}}
                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Mekanis</p>
                    @if($ceklist->mekanis)
                        <p class="text-lg font-bold text-gray-800 mb-4">{{ $ceklist->mekanis }}</p>
                    @else
                        <p class="text-sm text-gray-400 mb-4">-</p>
                    @endif
                    
                    @if($ceklist->ttd_mekanik)
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Tanda Tangan:</p>
                            <div class="border-2 border-cyan-200 rounded-lg overflow-hidden bg-white">
                                <a href="{{ asset('storage/' . $ceklist->ttd_mekanik) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $ceklist->ttd_mekanik) }}" 
                                        alt="TTD Mekanik" 
                                        class="w-full h-32 object-contain p-2 hover:opacity-80 transition-opacity cursor-pointer">
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-3 p-3 bg-gray-100 rounded-lg text-center">
                            <i class="bi bi-pen text-gray-400 text-2xl"></i>
                            <p class="text-xs text-gray-400 mt-1">Tidak ada tanda tangan</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($ceklist->keterangan)
            <div class="mt-6 p-5 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-2">
                    <i class="bi bi-info-circle-fill mr-1"></i>Keterangan
                </p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $ceklist->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>

</div>

@endsection
