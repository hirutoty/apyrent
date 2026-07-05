<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ApyRent Car</title>
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
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 relative overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/cars-banner.png') }}"
            class="w-full h-full object-cover scale-110 blur-2xl brightness-75" alt="bg">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/80 via-blue-950/60 to-black/80"></div>
    </div>

    {{-- Card --}}
    <div
        class="card-animate relative z-10 w-full max-w-4xl h-[500px]
                bg-white/[0.07] backdrop-blur-xl
                rounded-[32px] overflow-hidden flex
                border border-white/10
                shadow-[0_30px_100px_rgba(0,0,0,0.5)]
                hover:scale-[1.01] transition-transform duration-500">

        {{-- Panel Kiri (40%) --}}
        <div class="relative w-[40%] hidden md:block flex-shrink-0">
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
        <div class="flex-1 flex flex-col w-[40%] justify-center px-9 py-8 bg-white">

            {{-- Header --}}
            <div class="mb-5">
                <div class="md:hidden flex justify-center mt-6 mb-6">
                    <div class="w-64 flex justify-center pb-4 border-b-2 border-slate-900/40">
                        <img src="{{ asset('images/icon.png') }}" alt="APY Rent A Car"
                            class="h-14 w-auto object-contain">
                    </div>
                </div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Selamat Datang</h1>
                <p class="text-gray-400 text-xs mt-1">Masuk untuk mengakses dashboard rental kendaraan.</p>
            </div>

            {{-- Form --}}
            <form action="/login" method="POST" class="space-y-3">
                @csrf

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

                {{-- Password --}}
                <div>
                    {{-- <div class="flex items-center justify-between mb-1.5">
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-widest">Password</label>
                        <a href="/forgot-password"
                            class="text-[11px] text-blue-500 hover:text-blue-600 hover:underline transition-colors">Lupa
                            password?</a>
                    </div> --}}
                    <div
                        class="group flex items-center bg-gray-50 border border-gray-200/80 rounded-xl px-3.5 py-2.5
                                hover:border-blue-300 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20
                                transition-all duration-300">
                        <i
                            class="fa-solid fa-lock text-gray-300 group-focus-within:text-blue-400 mr-2.5 text-sm transition-colors duration-300"></i>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="flex-1 text-sm text-gray-700 placeholder-gray-300 outline-none bg-transparent" />
                        <button type="button" onclick="togglePassword()"
                            class="text-gray-300 hover:text-gray-500 transition-colors duration-200 ml-1">
                            <i id="eye-icon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Ingat Saya --}}
                <div class="flex items-center gap-2 pt-0.5">
                    <input type="checkbox" id="remember" name="remember" required
                        class="w-3.5 h-3.5 accent-blue-500 rounded cursor-pointer">
                    
                    <label for="remember" class="text-xs text-gray-400 cursor-pointer select-none">Ingat saya</label>
                </div>

                {{-- Tombol Masuk --}}
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
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk
                </button>

                {{-- Divider --}}
                <div class="flex items-center gap-3 py-0.5">
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-[11px] text-gray-300 font-medium">atau</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                {{-- Tombol Register --}}
                <a href="/register"
                    class="group w-full flex items-center justify-center gap-2
                          border border-gray-200 bg-white
                          text-gray-500 font-medium py-2.5 rounded-xl text-sm
                          hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600
                          hover:shadow-[0_4px_16px_rgba(59,130,246,0.15)]
                          hover:-translate-y-0.5
                          transition-all duration-300">
                    <i class="fa-solid fa-user-plus transition-transform duration-300 group-hover:scale-110"></i>
                    Buat Akun Baru
                </a>

            </form>

            {{-- Footer --}}
            <p class="text-center text-[10px] text-gray-300 mt-4">
                © {{ date('Y') }} ApyRent Car · Semua hak dilindungi
            </p>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // SweetAlert2 Toast config
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showClass: {
                popup: 'swal2-show'
            },
            hideClass: {
                popup: 'swal2-hide'
            },
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
