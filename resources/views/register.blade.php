<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ApyRent Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-animate {
            animation: fadeInUp 0.55s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.35);
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.55);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-6 relative overflow-y-auto">

    {{-- Background --}}
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('images/cars-banner.png') }}"
            class="w-full h-full object-cover scale-110 blur-2xl brightness-75" alt="bg">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/80 via-blue-950/60 to-black/80"></div>
    </div>

    {{-- Card --}}
    <div
    class="card-animate relative z-10
    w-full max-w-4xl
    h-auto
    max-h-[92vh]
    bg-white/[0.07]
    backdrop-blur-xl
    rounded-[32px]
    overflow-hidden
    flex
    border border-white/10
    shadow-[0_30px_100px_rgba(0,0,0,0.5)]">

        {{-- Panel Kiri (40%) --}}
        <div class="relative w-[40%] hidden md:flex flex-col flex-shrink-0">
            <img src="{{ asset('images/cars-banner.png') }}" alt="ApyRent Car Fleet"
                class="w-full h-full object-cover" />

            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent">

                {{-- Logo Pojok Kiri Atas --}}
                <div class="absolute top-6 left-6">
                    <div class="pb-2 border-b-2 border-white/30">
                         @if ($globalSetting?->logo)
                             <img src="{{ asset($globalSetting->logo) }}"
                                alt="{{ $globalSetting->nama_perusahaan }}" class="h-12 w-auto object-contain">
                        @else
                            <span class="text-white font-bold text-lg">
                                {{ $globalSetting?->nama_perusahaan ?? 'Rental App' }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Konten Bawah --}}
                <div class="absolute bottom-7 left-7">
                    <h2 class="text-white text-xl font-bold leading-snug">
                        Solusi Rental<br>Kendaraan Modern
                    </h2>
                    <p class="text-white/60 text-xs mt-2 leading-relaxed">
                        Kelola armada, pelanggan & transaksi<br>
                        dalam satu platform terintegrasi.
                    </p>
                </div>

            </div>
        </div>

        {{-- Panel Kanan (60%) --}}
        <div class="flex-1 px-9 py-7 bg-white overflow-y-auto">

            {{-- Header --}}
            <div class="mb-5">
                <div class="md:hidden flex justify-center mt-6 mb-6">
                    <div class="w-64 flex justify-center pb-4 border-b-2 border-slate-900/40">
                        <img src="{{ asset('images/icon.png') }}" alt="APY Rent A Car"
                            class="h-14 w-auto object-contain">
                    </div>
                </div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Bergabung Sekarang, Bersama kami!</h1>
                <p class="text-gray-400 text-xs mt-1">Buat akun baru untuk melanjutkan dengan mudah.</p>
            </div>

            {{-- Form --}}
            <form action="/register" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf

                {{-- Nama & Username --}}
                <div class="grid grid-cols-2 gap-3">
                    {{-- Nama --}}
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Nama</label>
                        <div
                            class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                    hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                    transition-all duration-300">
                            <i
                                class="fa-regular fa-user text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap"
                                class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                        </div>
                    </div>

                    {{-- Username --}}
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Username</label>
                        <div
                            class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                    hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                    transition-all duration-300">
                            <i
                                class="fa-solid fa-at text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                            <input type="text" name="username" value="{{ old('username') }}" placeholder="username"
                                class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Email</label>
                    <div
                        class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                transition-all duration-300">
                        <i
                            class="fa-regular fa-envelope text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                            class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                    </div>
                </div>

                {{-- No Telepon & Foto --}}
                <div class="grid grid-cols-2 gap-3">
                    {{-- No Telp --}}
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">No.
                            Telepon</label>
                        <div
                            class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                    hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                    transition-all duration-300">
                            <i
                                class="fa-solid fa-phone text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                            <input type="number" name="no_telp" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx"
                                class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                        </div>
                    </div>

                    {{-- Foto --}}
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Foto
                            Profil</label>
                        <label
                            class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                     hover:border-blue-300 focus-within:border-blue-500
                                     transition-all duration-300 cursor-pointer">
                            <i
                                class="fa-regular fa-image text-gray-300 group-hover:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                            <span id="foto-label" class="flex-1 text-sm text-gray-300 truncate">Pilih foto</span>
                            <input type="file" name="foto" accept="image/*" class="hidden"
                                onchange="document.getElementById('foto-label').textContent = this.files[0]?.name ?? 'Pilih foto'" />
                        </label>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Password</label>
                    <div
                        class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                transition-all duration-300">
                        <i
                            class="fa-solid fa-lock text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                        <input type="password" name="password" id="password" placeholder="Min. 8 karakter"
                            class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                        <button type="button" onclick="togglePass('password','eye1')"
                            class="text-gray-300 hover:text-gray-500 transition-colors duration-200 ml-1">
                            <i id="eye1" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Konfirmasi
                        Password</label>
                    <div
                        class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                transition-all duration-300">
                        <i
                            class="fa-solid fa-shield-halved text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Ulangi password"
                            class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                        <button type="button" onclick="togglePass('password_confirmation','eye2')"
                            class="text-gray-300 hover:text-gray-500 transition-colors duration-200 ml-1">
                            <i id="eye2" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Tombol Daftar --}}
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2
                               bg-gradient-to-r from-blue-600 to-indigo-600
                               hover:from-blue-700 hover:to-indigo-700
                               text-white font-semibold py-2.5 rounded-xl text-sm
                               shadow-[0_4px_20px_rgba(59,130,246,0.4)]
                               hover:shadow-[0_8px_28px_rgba(59,130,246,0.5)]
                               hover:-translate-y-1 hover:scale-[1.01]
                               active:scale-[0.98]
                               transition-all duration-300">
                    <i class="fa-solid fa-user-plus"></i>
                    Daftar Sekarang
                </button>

                {{-- Divider --}}
                <div class="flex items-center gap-3 py-0.5">
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-[11px] text-gray-300 font-medium">atau</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                {{-- Tombol Login --}}
                <a href="/login"
                    class="group w-full flex items-center justify-center gap-2
                          border border-gray-200 bg-white
                          text-gray-500 font-medium py-2.5 rounded-xl text-sm
                          hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600
                          hover:shadow-[0_4px_16px_rgba(59,130,246,0.15)]
                          hover:-translate-y-0.5
                          transition-all duration-300">
                    <i
                        class="fa-solid fa-right-to-bracket transition-transform duration-300 group-hover:scale-110"></i>
                    Sudah Punya Akun? Masuk
                </a>

            </form>

            {{-- Footer --}}
            <p class="text-center text-[10px] text-gray-300 mt-4">
                © {{ date('Y') }} ApyRent Car · Semua hak dilindungi
            </p>

        </div>
    </div>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if ($errors->any())
            @php
                $errList = $errors->all();
                $html = count($errList) > 1 ? '<ul style="text-align:left;margin:0;padding-left:1.2rem">' . implode('', array_map(fn($e) => "<li style='margin-bottom:4px'>$e</li>", $errList)) . '</ul>' : e($errList[0]);
            @endphp
            Toast.fire({
                icon: 'error',
                title: '{!! addslashes($html) !!}'
            });
        @endif

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ addslashes(session('success')) }}'
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ addslashes(session('error')) }}'
            });
        @endif

        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{ addslashes(session('warning')) }}'
            });
        @endif
    </script>

</body>

</html>
