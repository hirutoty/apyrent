<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ApyRent Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-7px);
            }
        }

        .anim-1 {
            animation: fadeInUp 0.55s cubic-bezier(.22, .68, 0, 1.2) 0.1s both;
        }

        .anim-2 {
            animation: fadeInUp 0.55s cubic-bezier(.22, .68, 0, 1.2) 0.22s both;
        }

        .anim-3 {
            animation: fadeInUp 0.55s cubic-bezier(.22, .68, 0, 1.2) 0.34s both;
        }

        .anim-4 {
            animation: fadeInUp 0.55s cubic-bezier(.22, .68, 0, 1.2) 0.46s both;
        }

        .anim-5 {
            animation: fadeInUp 0.55s cubic-bezier(.22, .68, 0, 1.2) 0.58s both;
        }

        .bg-fade {
            animation: fadeIn 1s ease both;
        }

        .float {
            animation: floatY 4s ease-in-out infinite;
        }

        .feature-card {
            transition: transform 0.3s ease, background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body class="overflow-hidden min-h-screen">

    {{-- Background --}}
    <div class="fixed inset-0 z-0 bg-fade">
            <img src="{{ asset($globalSetting->logo) }}"
            class="w-full h-full object-cover scale-110 blur-2xl brightness-75" alt="bg" />
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/80 via-blue-950/60 to-black/80"></div>
        {{-- orbs --}}
        <div class="absolute -top-28 -right-28 w-96 h-96 rounded-full bg-blue-700/10 blur-3xl pointer-events-none">
        </div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-indigo-700/10 blur-3xl pointer-events-none">
        </div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-4">



        {{-- Card --}}
        <div
            class="anim-2 w-full max-w-md
                    bg-white/[0.07] border border-white/10 rounded-[32px] p-10 text-center
                    backdrop-blur-xl
                    shadow-[0_30px_80px_rgba(0,0,0,0.5)]
                    hover:scale-[1.01] transition-transform duration-500">

            {{-- Hero icon --}}
            <div class="anim-3 flex justify-center mb-5">
    <img
        <img src="{{ asset($globalSetting->logo) }}"
        alt="APY Rent A Car"
        class="h-14 w-auto object-contain">
</div>

            {{-- Title --}}
            <h1 class="anim-3 text-white text-[22px] font-bold tracking-tight mb-1">
                Selamat Datang
            </h1>
            

            {{-- Description --}}
            <p class="anim-3 text-white/50 text-sm leading-relaxed mb-8">
                Nikmati kemudahan mengelola armada, pelanggan,<br>
                dan transaksi rental —
                <span class="text-white/75 font-medium">cepat, aman, dan efisien.</span>
            </p>

            {{-- Feature Cards --}}
            <div class="anim-4 grid grid-cols-3 gap-3 mb-8">
                <div
                    class="feature-card bg-white/[0.05] border border-white/10 rounded-2xl py-5 px-2
                            flex flex-col items-center gap-2.5 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-lg text-white/70"></i>
                    </div>
                    <span class="text-white/55 text-[11px] font-medium leading-tight">Keamanan<br>Data</span>
                </div>
                <div
                    class="feature-card bg-white/[0.05] border border-white/10 rounded-2xl py-5 px-2
                            flex flex-col items-center gap-2.5 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-lg text-white/70"></i>
                    </div>
                    <span class="text-white/55 text-[11px] font-medium leading-tight">Laporan<br>Real-time</span>
                </div>
                <div
                    class="feature-card bg-white/[0.05] border border-white/10 rounded-2xl py-5 px-2
                            flex flex-col items-center gap-2.5 cursor-default">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                        <i class="fa-solid fa-table-columns text-lg text-white/70"></i>
                    </div>
                    <span class="text-white/55 text-[11px] font-medium leading-tight">Dashboard<br>Mudah</span>
                </div>
            </div>

            {{-- CTA --}}
            <a href="{{ route('login') }}"
                class="anim-5 w-full flex items-center justify-center gap-2
                      bg-gradient-to-r from-blue-600 to-indigo-600
                      hover:from-blue-700 hover:to-indigo-700
                      text-white font-semibold py-3 rounded-xl text-sm
                      shadow-[0_4px_20px_rgba(59,130,246,0.4)]
                      hover:shadow-[0_8px_28px_rgba(59,130,246,0.5)]
                      hover:-translate-y-1 hover:scale-[1.01]
                      active:scale-[0.98]
                      transition-all duration-300">
                <i class="fa-solid fa-right-to-bracket"></i>
                Mulai Sekarang
            </a>

        </div>

        {{-- Footer --}}
        <div class="anim-5 flex items-center gap-5 mt-7">
            
            <div class="w-px h-3 bg-white/10"></div>
            <span class="text-white/30 text-[11px]">© {{ date('Y') }} ApyRent Car · Semua hak dilindungi</span>
        </div>

    </div>

</body>

</html>
