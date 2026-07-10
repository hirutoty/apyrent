@extends('admin.layouts.app')

@section('title', 'Profile Saya')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="max-w-2xl mx-auto space-y-5">

    {{-- HEADER CARD --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Banner --}}
        <div class="h-24 bg-gradient-to-r from-blue-500 to-blue-700"></div>

        {{-- Avatar & Info --}}
        <div class="px-6 pb-5">
            <div class="flex items-end justify-between -mt-10 mb-4">
                    <div class="relative">

    <img
        id="previewFoto"
        src="{{ $user->foto ? asset($user->foto) : asset('images/default-user.png') }}"
        class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-sm">

    <label for="fotoInput"
        class="absolute -bottom-1.5 -right-1.5 w-7 h-7 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center cursor-pointer shadow transition-colors">
        <i class="fa fa-camera text-xs"></i>
    </label>

</div>
                <div class="mb-1 text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>
                        {{ ucfirst($user->role ?? 'Admin') }}
                    </span>
                </div>
            </div>
            <h2 class="text-base font-bold text-gray-800">{{ $user->name }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
        </div>
    </div>

    {{-- FORM CARD --}}
    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Input foto tersembunyi --}}
        <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*">

        {{-- Informasi Akun --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fa fa-user text-blue-500 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Informasi Akun</h3>
                    <p class="text-xs text-gray-400">Data pribadi & kontak</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa fa-id-card text-gray-400 mr-1"></i> Nama Lengkap
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        placeholder="Nama lengkap"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa fa-at text-gray-400 mr-1"></i> Username
                    </label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        placeholder="Username"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa fa-envelope text-gray-400 mr-1"></i> Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        placeholder="email@domain.com"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa fa-phone text-gray-400 mr-1"></i> No Telepon
                    </label>
                    <input type="number" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                        placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                </div>

            </div>
        </div>

        {{-- Ubah Password --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-5">

            <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="fa fa-lock text-amber-500 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Ubah Password</h3>
                    <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengubah</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa fa-key text-gray-400 mr-1"></i> Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput"
                            placeholder="••••••••"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                        <button type="button" onclick="togglePassword('passwordInput', 'eyeIcon1')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fa fa-eye text-sm" id="eyeIcon1"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa fa-key text-gray-400 mr-1"></i> Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="passwordConfirm"
                            placeholder="••••••••"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition">
                        <button type="button" onclick="togglePassword('passwordConfirm', 'eyeIcon2')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fa fa-eye text-sm" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex items-center justify-end gap-3 mt-5">
            <a href="/admin/dashboard"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <i class="fa fa-arrow-left text-xs"></i> Kembali
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-save text-sm"></i> Simpan Perubahan
            </button>
        </div>

    </form>

</div>


{{-- POPUP ALERT --}}
@if (session('success') || session('error') || $errors->any())
    <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
        style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
        <div id="alertBox"
            class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
            style="transform:translateY(-16px);transition:transform 0.25s">

            @if (session('success'))
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            @elseif (session('error'))
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            @else
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                    <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
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
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Toggle show/hide password
    function togglePassword(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type  = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type  = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Popup alert
    (function() {
        var overlay = document.getElementById('alertOverlay');
        var box     = document.getElementById('alertBox');
        if (!overlay) return;

        setTimeout(function() {
            overlay.style.opacity       = '1';
            overlay.style.pointerEvents = 'auto';
            box.style.transform         = 'translateY(0)';
        }, 80);

        var timer = setTimeout(closeAlert, 4500);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeAlert();
        });

        function closeAlert() {
            clearTimeout(timer);
            overlay.style.opacity       = '0';
            overlay.style.pointerEvents = 'none';
            box.style.transform         = 'translateY(-16px)';
        }
        window.closeAlert = closeAlert;
    })();
    
    const input = document.getElementById('fotoInput');
    const preview = document.getElementById('previewFoto');
    
    input.addEventListener('change', function () {
    
        if (!this.files || !this.files[0]) return;
    
        preview.src = URL.createObjectURL(this.files[0]);
    
    });
</script>

@endsection