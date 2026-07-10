@extends('admin.layouts.app')

@section('title', 'Setting')

@section('content')


    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        @if (session('success') || session('error') || $errors->any())
            <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
                style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

                <div id="alertBox"
                    class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                    style="transform:translateY(-16px);transition:transform 0.25s">

                    @if (session('success'))
                        <div
                            class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                            <i class="fa fa-check-circle"></i>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                                {{ session('success') }}
                            </p>
                        </div>
                    @else
                        <div
                            class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                            <i class="fa fa-exclamation-circle"></i>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>

                            @if (session('error'))
                                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                                    {{ session('error') }}
                                </p>
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
                        class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
                        <i class="fa fa-times"></i>
                    </button>

                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">
                Konfigurasi Perusahaan
            </h2>
            <p class="text-xs text-gray-500 mt-1">
                Kelola informasi perusahaan yang digunakan pada invoice, PDF, dan laporan.
            </p>
        </div>

        {{-- Form --}}
        <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-100">
    @csrf

    {{-- Informasi Perusahaan --}}
    <div class="p-6 space-y-5">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7l9-4 9 4v14M9 21v-6h6v6"/>
            </svg>
            Informasi perusahaan
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Nama perusahaan</label>
                <input type="text" name="nama_perusahaan"
                    value="{{ old('nama_perusahaan', $setting?->nama_perusahaan) }}"
                    placeholder="PT. Contoh Indonesia"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Telepon</label>
                <input type="number" name="telepon"
                    value="{{ old('telepon', $setting?->telepon) }}"
                    placeholder="+62 21 1234 5678"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Email</label>
                <input type="email" name="email"
                    value="{{ old('email', $setting?->email) }}"
                    placeholder="info@perusahaan.com"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Website</label>
                <input type="text" name="website"
                    value="{{ old('website', $setting?->website) }}"
                    placeholder="https://perusahaan.com"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-gray-500">Alamat perusahaan</label>
            <textarea name="alamat" rows="3"
                placeholder="Jl. Sudirman No. 1, Jakarta Pusat..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition resize-none">{{ old('alamat', $setting?->alamat) }}</textarea>
        </div>
    </div>

    {{-- Informasi Bank --}}
    <div class="p-6 space-y-5">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 7h20M5 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2M3 7l1 13h16l1-13M9 11v5m6-5v5"/>
            </svg>
            Informasi rekening bank
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Nama bank</label>
                <input type="text" name="nama_bank"
                    value="{{ old('nama_bank', $setting?->nama_bank) }}"
                    placeholder="Bank Central Asia"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Nomor rekening</label>
                <input type="text" name="nomor_rekening"
                    value="{{ old('nomor_rekening', $setting?->nomor_rekening) }}"
                    placeholder="1234567890"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Atas nama</label>
                <input type="text" name="atas_nama_rekening"
                    value="{{ old('atas_nama_rekening', $setting?->atas_nama_rekening) }}"
                    placeholder="PT. Contoh Indonesia"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
            </div>
        </div>
    </div>

    {{-- Logo Perusahaan --}}
    <div class="p-6 space-y-5">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5zm0 9 4-4 4 4 4-6 4 6"/>
            </svg>
            Logo perusahaan
        </div>

        <div class="flex items-start gap-5 p-4 bg-gray-50 border border-gray-200 rounded-xl">
            {{-- Preview --}}
            @if ($setting?->logo)
                <img src="{{ asset($setting->logo) }}" alt="Logo Perusahaan"
                    class="w-20 h-20 object-contain rounded-lg border border-gray-200 bg-white p-2 flex-shrink-0">
            @else
                <div class="w-20 h-20 flex-shrink-0 rounded-lg border border-dashed border-gray-300 bg-white flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5zm0 9 4-4 4 4 4-6 4 6"/>
                    </svg>
                </div>
            @endif

            {{-- Upload --}}
            <div class="flex-1 space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Upload logo baru</label>
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                <p class="text-xs text-gray-400">Format JPG, PNG, atau WEBP · Maksimal 2 MB</p>
            </div>
        </div>
    </div>

    {{-- Batas Reminder --}}
    <div class="p-6 space-y-5">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
            </svg>
            Batas reminder
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Batas</label>
                <input type="number" name="batas_reminder" min="1"
                    value="{{ old('batas_reminder', $setting->batas_reminder ?? 1) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                @error('batas_reminder')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-500">Satuan Reminder</label>
                <select name="satuan_reminder"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition bg-white">
                    @foreach (['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan', 'tahun' => 'Tahun'] as $value => $label)
                        <option value="{{ $value }}"
                            {{ old('satuan_reminder', $setting->satuan_reminder ?? 'hari') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('satuan_reminder')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="px-6 py-4 flex justify-end bg-gray-50 rounded-b-xl">
        <button type="submit"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2zM12 17v-6m-3 3h6"/>
            </svg>
            Simpan konfigurasi
        </button>
    </div>

</form>

    </div>
    <script>
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box = document.getElementById('alertBox');

            if (!overlay) return;

            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);

            var timer = setTimeout(closeAlert, 3000);

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeAlert();
            });

            function closeAlert() {
                clearTimeout(timer);

                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                box.style.transform = 'translateY(-16px)';

                setTimeout(function() {
                    window.location.reload();
                }, 300);
            }

            window.closeAlert = closeAlert;
        })();
    </script>

@endsection
