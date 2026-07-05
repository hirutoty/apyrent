<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $globalSetting->nama_perusahaan ?? 'Rental App' }}</title>

    @if (!empty($globalSetting?->logo))
        <link rel="icon" type="image/png" href="{{ asset($globalSetting->logo) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['DM Mono', 'monospace'],
                    },
                    colors: {
                        sidebar: {
                            bg: '#0f1117',
                            hover: '#1a1d27',
                            active: '#1e2235',
                            border: '#1f2333',
                            text: '#8b93b0',
                            label: '#4a5175',
                        },
                        accent: {
                            DEFAULT: '#4f6ef7',
                            light: '#eef1ff',
                            hover: '#3b5bdb',
                            dim: '#1e2a5e',
                        },
                        surface: '#ffffff',
                        bg: '#f0f2f8',
                        ink: {
                            DEFAULT: '#12172e',
                            sub: '#4a5175',
                            muted: '#9aa0bb',
                        },
                        border: {
                            DEFAULT: '#e2e6f3',
                            light: '#edf0f8',
                        },
                    },
                    transitionDuration: {
                        250: '250ms'
                    },
                    keyframes: {
                        'slide-in': {
                            '0%': {
                                transform: 'translateX(-100%)'
                            },
                            '100%': {
                                transform: 'translateX(0)'
                            }
                        },
                        'fade-in': {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(-6px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        'pulse-dot': {
                            '0%,100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            },
                            '50%': {
                                transform: 'scale(1.4)',
                                opacity: '0.7'
                            }
                        },
                    },
                    animation: {
                        'slide-in': 'slide-in 250ms cubic-bezier(0.4,0,0.2,1)',
                        'fade-in': 'fade-in 200ms ease forwards',
                        'pulse-dot': 'pulse-dot 1.5s ease-in-out infinite',
                    },
                }
            }
        }
    </script>

    <style>
        body {
            font-size: 14px;
        }

        /* ── Sidebar scrollbar ── */
        #sidebar {
            transition: transform 280ms cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #2a2f45 transparent;
        }

        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #2a2f45;
            border-radius: 999px;
        }

        /* ── Nav link active ── */
        .nav-link.active {
            background: #1e2235;
            color: #fff;
            border-left: 3px solid #4f6ef7;
            padding-left: calc(1rem - 3px);
        }

        /* ── Sidebar overlay ── */
        #sidebar-overlay {
            transition: opacity 280ms ease;
        }

        /* ── Prevent body scroll when sidebar open on mobile ── */
        body.sidebar-open {
            overflow: hidden;
        }

        /* ── Main content scrollbar ── */
        #main-content::-webkit-scrollbar {
            width: 5px;
        }

        #main-content::-webkit-scrollbar-track {
            background: #f0f2f8;
        }

        #main-content::-webkit-scrollbar-thumb {
            background: #d1d5e8;
            border-radius: 999px;
        }

        #main-content::-webkit-scrollbar-thumb:hover {
            background: #9aa0bb;
        }

        /* ── Hamburger icon morphing ── */
        .ham-line {
            display: block;
            width: 18px;
            height: 2px;
            background: currentColor;
            border-radius: 2px;
            transition: transform 250ms ease, opacity 250ms ease, width 250ms ease;
        }

        .ham-line-1 {
            transform-origin: center;
        }

        .ham-line-2 {
            width: 13px;
        }

        .ham-line-3 {
            transform-origin: center;
        }

        body.sidebar-desktop-collapsed .ham-line-1 {
            transform: translateY(4px) rotate(45deg);
            width: 18px;
        }

        body.sidebar-desktop-collapsed .ham-line-2 {
            opacity: 0;
            width: 0;
        }

        body.sidebar-desktop-collapsed .ham-line-3 {
            transform: translateY(-4px) rotate(-45deg);
            width: 18px;
        }

        /* Mobile hamburger open state */
        body.sidebar-open .ham-line-1 {
            transform: translateY(4px) rotate(45deg);
            width: 18px;
        }

        body.sidebar-open .ham-line-2 {
            opacity: 0;
            width: 0;
        }

        body.sidebar-open .ham-line-3 {
            transform: translateY(-4px) rotate(-45deg);
            width: 18px;
        }

        /* ── Desktop collapsed sidebar ── */
        @media (min-width: 1024px) {
            body.sidebar-desktop-collapsed #sidebar {
                transform: translateX(-100%);
            }

            body.sidebar-desktop-collapsed .lg\:pl-64 {
                padding-left: 0 !important;
                transition: padding 280ms cubic-bezier(0.4, 0, 0.2, 1);
            }

            .lg\:pl-64 {
                transition: padding 280ms cubic-bezier(0.4, 0, 0.2, 1);
            }
        }

        /* ── Profile dropdown ── */
        #profile-dropdown {
            transform-origin: top left;
            animation: fade-in 180ms ease forwards;
        }

        /* ── Navbar gradient border bottom ── */
        .navbar-border::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e6f3 20%, #e2e6f3 80%, transparent);
        }

        /* ── Badge notification pulse ── */
        .notif-dot {
            animation: pulse-dot 1.5s ease-in-out infinite;
        }

        /* ── Tooltip ── */
        [data-tip] {
            position: relative;
        }

        [data-tip]::after {
            content: attr(data-tip);
            position: absolute;

            top: calc(100% + 6px);
            /* dari bottom menjadi top */
            left: 50%;

            transform: translateX(-50%) translateY(-4px);

            background: #12172e;
            color: #fff;
            font-size: 11px;
            white-space: nowrap;
            padding: 3px 8px;
            border-radius: 6px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 150ms ease, transform 150ms ease;
        }

        [data-tip]:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ── Nav group (dropdown) ── */
        .nav-group {
            margin-bottom: 2px;
        }

        .nav-group-toggle {
            cursor: pointer;
            background: transparent;
            border: none;
        }

        .nav-group-toggle .nav-group-chevron {
            transition: transform 220ms ease;
        }

        .nav-group.open .nav-group-toggle .nav-group-chevron {
            transform: rotate(180deg);
        }

        .nav-group.open .nav-group-toggle {
            color: #fff;
        }

        .nav-group-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 280ms ease;
        }

        .nav-subgroup-label {
            padding: 6px 12px 4px 30px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #4a5175;
        }

        .nav-group-content .nav-link {
            font-size: 12.5px;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .nav-group-content .nav-link.active {
            padding-left: calc(1rem - 3px);
        }

        .nav-group-warning-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #f87171;
            margin-left: auto;
            flex-shrink: 0;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-bg font-sans text-ink antialiased">

    {{-- ══ SIDEBAR OVERLAY (mobile) ══ --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 opacity-0 pointer-events-none lg:hidden"
        onclick="closeSidebar()">
    </div>

    {{-- ══ SIDEBAR ══ --}}
    <aside id="sidebar"
        class="fixed top-0 left-0 h-full w-64 bg-sidebar-bg z-50 flex flex-col -translate-x-full lg:translate-x-0">

        {{-- Logo area --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-sidebar-border shrink-0">
            <div class="w-full">
                <div class="flex flex-col items-center text-center">
                    @php
                        $setting = \App\Models\Setting::first();
                    @endphp

                    @if ($setting && $setting->logo)
                        <img src="{{ asset($setting->logo) }}"
                            alt="{{ $setting->nama_perusahaan ?? 'Logo Perusahaan' }}"
                            class="h-10 w-auto object-contain">
                    @endif
                    <div class="w-40 h-px bg-white/10 mt-3"></div>
                    <h2 class="mt-3 text-sm font-bold text-white">Rental Management System</h2>
                    <p class="text-[10px] text-sidebar-label tracking-[0.2em] uppercase">Vehicle Rental Platform</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        @php
            $role = auth()->user()->role;

            // Warning flags dipakai di dalam grup "Operasi" (bagian Service)
            $hasWarning   = isset($serviceWarning) && count($serviceWarning) > 0;
            $isKirWarning = isset($kirWarning) && $kirWarning;
            $operasiHasWarning = $hasWarning || $isKirWarning;
        @endphp

        <nav id="sidebar-nav" class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            {{-- ════════════════════════════════════
                 DASHBOARD (link langsung, tanpa dropdown)
            ════════════════════════════════════ --}}
            @if ($role == 'superadmin' || $role == 'keuangan')
                <a href="/admin/dashboard"
                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all text-[13px] font-semibold">
                    <i class="bi bi-grid-1x2-fill w-4 text-center shrink-0"></i>
                    <span class="flex-1 text-left">Dashboard</span>
                </a>
            @endif

            {{-- ════════════════════════════════════
                 GRUP: KONFIGURASI
            ════════════════════════════════════ --}}
            @if ($role == 'superadmin')
                <div class="nav-group">
                    <button type="button" onclick="toggleGroup(this)"
                        class="nav-group-toggle w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all text-[13px] font-semibold">
                        <i class="bi bi-sliders w-4 text-center shrink-0"></i>
                        <span class="flex-1 text-left">Konfigurasi</span>
                        <i class="bi bi-chevron-down text-[10px] nav-group-chevron"></i>
                    </button>
                    <div class="nav-group-content">
                        <div class="pt-0.5 pb-1 space-y-0.5">
                            <a href="/admin/setting"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-sliders w-4 text-center shrink-0"></i> Setting
                            </a>
                            <a href="/profil"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="fa-solid fa-user-circle w-4 text-center shrink-0"></i> Profile
                            </a>
                            <a href="/admin/user"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="fa-solid fa-user-gear w-4 text-center shrink-0"></i> User
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════
                     GRUP: MASTER DATA
                ════════════════════════════════════ --}}
                <div class="nav-group">
                    <button type="button" onclick="toggleGroup(this)"
                        class="nav-group-toggle w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all text-[13px] font-semibold">
                        <i class="bi bi-boxes w-4 text-center shrink-0"></i>
                        <span class="flex-1 text-left">Master Data</span>
                        <i class="bi bi-chevron-down text-[10px] nav-group-chevron"></i>
                    </button>
                    <div class="nav-group-content">
                        <div class="pt-0.5 pb-1 space-y-0.5">
                            <a href="/admin/jenis-asuransi"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-ui-checks-grid w-4 text-center shrink-0"></i> Jenis Asuransi
                            </a>
                            <a href="/admin/asuransi"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-shield-check w-4 text-center shrink-0"></i> Asuransi
                            </a>
                            <a href="/admin/gps"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-geo-alt-fill w-4 text-center shrink-0"></i> GPS
                            </a>
                            <a href="/admin/supplier"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-building-fill w-4 text-center shrink-0"></i> Supplier
                            </a>
                            <a href="/admin/jenis"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-tags-fill w-4 text-center shrink-0"></i> Jenis Kendaraan
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════
                 GRUP: OPERASI
                 (gabungan Kendaraan + Rental + Service)
            ════════════════════════════════════ --}}
            @if ($role == 'superadmin' || $role == 'keuangan' || $role == 'produksi')
                <div class="nav-group">
                    <button type="button" onclick="toggleGroup(this)"
                        class="nav-group-toggle w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all text-[13px] font-semibold">
                        <i class="bi bi-gear-wide-connected w-4 text-center shrink-0"></i>
                        <span class="flex-1 text-left">Operasi</span>
                        @if ($operasiHasWarning)
                            <span class="nav-group-warning-dot notif-dot"></span>
                        @endif
                        <i class="bi bi-chevron-down text-[10px] nav-group-chevron"></i>
                    </button>
                    <div class="nav-group-content">
                        <div class="pt-0.5 pb-1 space-y-0.5">

                            {{-- Sub: Kendaraan --}}
                            @if ($role == 'superadmin')
                                <p class="nav-subgroup-label">Kendaraan</p>
                                <a href="/admin/kendaraan"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-truck-front-fill w-4 text-center shrink-0"></i> Data & Stok Kendaraan
                                </a>
                                <a href="/admin/pajak"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-cash-stack w-4 text-center shrink-0"></i> Pajak Kendaraan
                                </a>
                                <a href="/admin/asuransi-kendaraan"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-car-front-fill w-4 text-center shrink-0"></i> Asuransi Kendaraan
                                </a>
                                <a href="/admin/gps-kendaraan"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-broadcast-pin w-4 text-center shrink-0"></i> GPS Kendaraan
                                </a>
                                <a href="/admin/procuremento"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-box-seam w-4 text-center shrink-0"></i> Pengadaan
                                </a>
                                <a href="/admin/purchasero"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-cart-check w-4 text-center shrink-0"></i> Pembelian
                                </a>
                                <a href="/admin/vendoreo"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-building w-4 text-center shrink-0"></i> Vendor
                                </a>
                            @endif

                            {{-- Sub: Rental --}}
                            @if ($role == 'superadmin' || $role == 'keuangan')
                                <p class="nav-subgroup-label">Rental</p>
                                <a href="/admin/member"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-people-fill w-4 text-center shrink-0"></i> Member
                                </a>
                                <a href="/admin/rental"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-calendar2-check-fill w-4 text-center shrink-0"></i> Booking Kendaraan
                                </a>
                                <a href="/admin/history"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-graph-up-arrow w-4 text-center shrink-0"></i> Kendaraan aktif & History
                                </a>
                            @endif

                            {{-- Sub: Service --}}
                            @if ($role == 'superadmin' || $role == 'produksi')
                                <p class="nav-subgroup-label">Service</p>

                                @php
                                    $isActive = request()->is('admin/service-history*');
                                @endphp

                                <a href="/admin/service-history"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all font-medium
                            {{ $hasWarning ? 'bg-red-500/20 text-red-200 hover:bg-red-500/30' : ($isActive ? 'bg-sidebar-hover text-white' : 'text-sidebar-text hover:bg-sidebar-hover hover:text-white') }}">
                                    <i class="bi bi-clock-history w-4 text-center shrink-0"></i>
                                    Service Kendaraan
                                    @if ($hasWarning)
                                        <i class="fas fa-triangle-exclamation text-red-400 animate-pulse ml-auto"></i>
                                    @endif
                                </a>

                                <a href="/admin/service-detail"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                    <i class="bi bi-file-text-fill w-4 text-center shrink-0"></i> Mobil Bermasalah
                                </a>

                                <a href="/admin/kir"
                                    class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all font-medium
                            {{ $isKirWarning ? 'bg-red-500/20 text-red-200 hover:bg-red-500/30' : 'text-sidebar-text hover:bg-sidebar-hover hover:text-white' }}">
                                    <i class="bi bi-clipboard2-check-fill w-4 text-center shrink-0"></i>
                                    KIR
                                    @if ($isKirWarning)
                                        <i class="fas fa-triangle-exclamation text-red-400 animate-pulse ml-auto"></i>
                                    @endif
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════
                 GRUP: HISTORY PERPANJANGAN
                 (dipisah dari Operasi, dropdown sendiri)
            ════════════════════════════════════ --}}
            @if ($role == 'superadmin' || $role == 'produksi')
                <div class="nav-group">
                    <button type="button" onclick="toggleGroup(this)"
                        class="nav-group-toggle w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all text-[13px] font-semibold">
                        <i class="fa-solid fa-clock-rotate-left w-4 text-center shrink-0"></i>
                        <span class="flex-1 text-left">History Perpanjangan</span>
                        <i class="bi bi-chevron-down text-[10px] nav-group-chevron"></i>
                    </button>
                    <div class="nav-group-content">
                        <div class="pt-0.5 pb-1 space-y-0.5">
                            <a href="gps-kendaraan-history"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="fa-solid fa-location-crosshairs w-4 text-center"></i>
                                History GPS
                            </a>

                            <a href="asuransi-history"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="fa-solid fa-shield-halved w-4 text-center"></i>
                                History Asuransi
                            </a>

                            <a href="pajak-history"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="fa-solid fa-receipt w-4 text-center"></i>
                                History Pajak
                            </a>

                            <a href="kir-history"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="fa-solid fa-file-lines w-4 text-center"></i>
                                History KIR
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════
                 GRUP: KEUANGAN
            ════════════════════════════════════ --}}
            @if ($role == 'superadmin' || $role == 'keuangan')
                <div class="nav-group">
                    <button type="button" onclick="toggleGroup(this)"
                        class="nav-group-toggle w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all text-[13px] font-semibold">
                        <i class="bi bi-cash-coin w-4 text-center shrink-0"></i>
                        <span class="flex-1 text-left">Keuangan</span>
                        <i class="bi bi-chevron-down text-[10px] nav-group-chevron"></i>
                    </button>
                    <div class="nav-group-content">
                        <div class="pt-0.5 pb-1 space-y-0.5">
                            <a href="/admin/keuangan"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-cash-stack w-4 text-center shrink-0"></i> Cash Flow
                            </a>

                            <a href="/admin/summary"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-receipt-cutoff"></i>
                                Invoice
                            </a>

                            <a href="/admin/aging_ap"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-bank2 w-4 text-center shrink-0"></i> Aging Ap
                            </a>
                            <a href="/admin/budgeting"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-wallet2 w-4 text-center shrink-0"></i> Budgeting
                            </a>
                            <a href="/admin/konsolidasi"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-diagram-3-fill w-4 text-center shrink-0"></i> Konsolidasi
                            </a>
                            <a href="/admin/efaktur"

                            class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text
                        hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                            <i class="bi bi-receipt w-4 text-center shrink-0"></i> E-Faktur & Bupot
                        </a>
                            <a href="/admin/rekonsiliasi"

                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-receipt w-4 text-center shrink-0"></i> E-Faktur
                            </a>
                            <a href="/admin/bupot"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-file-earmark-text-fill w-4 text-center shrink-0"></i> Bupot
                            </a>
                            <a href="/admin/integrasi-bank"

                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-bank2 w-4 text-center shrink-0"></i> Integrasi Bank
                            </a>
                            <a href="/admin/bukubesar"
                                class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sidebar-text hover:bg-sidebar-hover hover:text-white transition-all font-medium">
                                <i class="bi bi-journal-bookmark-fill w-4 text-center shrink-0"></i> Buku Besar
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </nav>

        {{-- User profile (bottom) --}}
        <div class="shrink-0 border-t border-sidebar-border p-3">
            <div
                class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-sidebar-hover transition-colors cursor-pointer">

                {{-- Avatar: foto jika ada, inisial jika tidak --}}
                @if (auth()->user()->foto)
                    <img src="{{ asset(auth()->user()->foto) }}"
                        class="w-8 h-8 rounded-lg object-cover shrink-0 border border-white/20">
                @else
                    <div
                        class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center font-bold text-white text-sm shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="text-white text-[13px] font-semibold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-sidebar-label text-[11px] truncate">{{ auth()->user()->role ?? 'Administrator' }}
                    </p>
                </div>
                <div class="w-2 h-2 rounded-full bg-emerald-400 shrink-0 notif-dot"></div>
            </div>
        </div>

    </aside>

    {{-- ══ MAIN WRAPPER ══ --}}
    <div id="main-wrapper" class="lg:pl-64 flex flex-col min-h-screen">

        {{-- ── NAVBAR ── --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md navbar-border relative shadow-sm">
            <div class="flex items-center h-14 px-4 sm:px-5 gap-3">

                {{-- ── Hamburger (works on ALL screen sizes) ── --}}
                <button id="hamburger-btn" onclick="toggleSidebar()" data-tip="Toggle Sidebar"
                    class="flex flex-col justify-center items-center gap-[5px] w-9 h-9 rounded-xl
                           bg-gray-50 hover:bg-accent/10 hover:text-accent
                           text-gray-500 border border-gray-200 hover:border-accent/30
                           transition-all duration-200 shrink-0 group"
                    aria-label="Toggle Sidebar">
                    <span class="ham-line ham-line-1"></span>
                    <span class="ham-line ham-line-2"></span>
                    <span class="ham-line ham-line-3"></span>
                </button>

                {{-- ── Divider ── --}}
                <div class="w-px h-5 bg-gray-200 shrink-0"></div>

                {{-- ── Brand / Breadcrumb area ── --}}
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <span class="hidden sm:inline-flex items-center gap-1.5 text-[12px] text-gray-400 font-medium">
                        <i class="bi bi-house-fill text-accent text-[11px]"></i>
                        <span class="text-gray-300">/</span>
                        <span class="text-gray-600 font-semibold" id="page-title">Dashboard</span>
                    </span>
                </div>

                {{-- ── RIGHT SIDE ── --}}
                <div class="flex items-center gap-2 shrink-0">

                    {{-- Date badge --}}
                    <div
                        class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gray-50 border border-gray-200 text-[11px] text-gray-500 font-medium">
                        <i class="bi bi-calendar3 text-accent text-[10px]"></i>
                        <span id="headerDate"></span>
                    </div>

                    {{-- Panduan --}}
                    <a href="/admin/panduan"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                               bg-accent/5 border border-accent/20
                               text-[12px] font-semibold text-accent
                               hover:bg-accent hover:text-white hover:border-accent
                               transition-all duration-200 group">
                        <i class="bi bi-book text-sm"></i>
                        <span>Panduan</span>
                        <i
                            class="bi bi-arrow-up-right text-[10px] transition-transform duration-200 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
                    </a>

                    {{-- Notification bell
                    <button data-tip="Notifikasi"
                        class="relative w-9 h-9 flex items-center justify-center rounded-xl
                               bg-amber-50 border border-amber-200 text-amber-500
                               hover:bg-amber-500 hover:text-white hover:border-amber-500
                               transition-all duration-200">
                        <i class="bi bi-bell-fill text-[14px]"></i>
                        <span
                            class="notif-dot absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button> --}}

                    {{-- ── Divider ── --}}
                    <div class="w-px h-5 bg-gray-200"></div>

                    {{-- Profile button + dropdown --}}
                    <div class="relative">
                        <button id="profile-btn" onclick="toggleDropdown()"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-xl
           hover:bg-gray-50 border border-transparent hover:border-gray-200
           transition-all duration-200 group">

                            {{-- Avatar: foto jika ada, inisial jika tidak --}}
                            @if (auth()->user()->foto)
                                <img src="{{ asset(auth()->user()->foto) }}"
                                    class="w-7 h-7 rounded-lg object-cover shrink-0 border border-gray-200">
                            @else
                                <div
                                    class="w-7 h-7 rounded-lg bg-accent flex items-center justify-center font-bold text-white text-sm shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </div>
                            @endif

                            <div class="hidden sm:block text-left">
                                <p class="text-[12px] font-semibold text-gray-700 leading-tight">
                                    {{ auth()->user()->name ?? 'Admin' }}</p>
                                <p class="text-[10px] text-gray-400 leading-tight capitalize">
                                    {{ auth()->user()->role ?? 'Administrator' }}</p>
                            </div>
                            <i
                                class="bi bi-chevron-down text-[10px] text-gray-400 hidden sm:block transition-transform duration-200 group-[.open]:rotate-180"></i>
                        </button>

                        {{-- Dropdown --}}
                        <div id="profile-dropdown"
                            class="hidden absolute right-0 top-full mt-2 w-52 bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/60 py-2 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 mb-1">
                                <p class="text-[13px] font-bold text-gray-800">{{ auth()->user()->name ?? 'Admin' }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ auth()->user()->email ?? '' }}</p>
                                <span
                                    class="inline-flex mt-1.5 items-center gap-1 px-2 py-0.5 rounded-full bg-accent/10 text-accent text-[10px] font-semibold capitalize">
                                    <i class="bi bi-shield-fill-check text-[9px]"></i>
                                    {{ auth()->user()->role ?? 'admin' }}
                                </span>
                            </div>
                            <a href="/profil"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <div class="w-6 h-6 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="bi bi-person-fill text-blue-500 text-[11px]"></i>
                                </div>
                                Profil Saya
                            </a>

                            <div class="border-t border-gray-100 my-1 mx-3"></div>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 transition-colors">
                                <div class="w-6 h-6 rounded-lg bg-red-50 flex items-center justify-center">
                                    <i class="bi bi-box-arrow-right text-red-500 text-[11px]"></i>
                                </div>
                                Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf</form>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        {{-- ── PAGE CONTENT ── --}}
        <main id="main-content" class="flex-1 p-4 sm:p-6 overflow-x-hidden">
            @yield('content')
        </main>

        {{-- ── FOOTER ── --}}
        <footer
            class="px-4 sm:px-6 py-3 border-t border-border bg-surface text-[11px] text-ink-muted flex flex-col sm:flex-row items-center justify-between gap-1">
            <span>© {{ date('Y') }} RentalApp. All rights reserved.</span>
            <span class="font-mono text-accent/60">v2.0.0</span>
        </footer>

    </div>

    {{-- ══ SCRIPTS ══ --}}
    <script>
        // ── Date header ──
        (function() {
            var m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            var d = new Date();
            var el = document.getElementById('headerDate');
            if (el) el.textContent = d.getDate() + ' ' + m[d.getMonth()] + ' ' + d.getFullYear();
        })();

        // ── Nav group (dropdown) toggle ──
        function toggleGroup(toggleBtn) {
            const group = toggleBtn.closest('.nav-group');
            const content = group.querySelector('.nav-group-content');
            const isOpen = group.classList.contains('open');

            if (isOpen) {
                content.style.maxHeight = '0px';
                group.classList.remove('open');
            } else {
                group.classList.add('open');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        }

        function openGroup(group) {
            const content = group.querySelector('.nav-group-content');
            group.classList.add('open');
            content.style.maxHeight = content.scrollHeight + 'px';
        }

        // ── Active nav link + auto-open parent group ──
        (function() {
            const path = window.location.pathname;
            document.querySelectorAll('#sidebar-nav .nav-link').forEach(link => {
                if (link.getAttribute('href') === path) {
                    link.classList.add('active');
                    const parentGroup = link.closest('.nav-group');
                    if (parentGroup) openGroup(parentGroup);
                }
            });
            // Set page title
            const active = document.querySelector('#sidebar-nav .nav-link.active');
            const titleEl = document.getElementById('page-title');
            if (active && titleEl) titleEl.textContent = active.textContent.trim();
        })();

        // ── Sidebar state ──
        // Desktop: track collapsed state in localStorage
        const DESKTOP_KEY = 'sidebar_desktop_collapsed';

        function initDesktopState() {
            if (window.innerWidth >= 1024) {
                const collapsed = localStorage.getItem(DESKTOP_KEY) === 'true';
                if (collapsed) document.body.classList.add('sidebar-desktop-collapsed');
            }
        }
        initDesktopState();

        function toggleSidebar() {
            if (window.innerWidth >= 1024) {
                // Desktop: collapse/expand sidebar
                const collapsed = document.body.classList.toggle('sidebar-desktop-collapsed');
                localStorage.setItem(DESKTOP_KEY, collapsed);
            } else {
                // Mobile: slide in/out
                const isOpen = document.body.classList.contains('sidebar-open');
                isOpen ? closeSidebar() : openSidebar();
            }
        }

        function openSidebar() {
            const overlay = document.getElementById('sidebar-overlay');
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            const overlay = document.getElementById('sidebar-overlay');
            document.getElementById('sidebar').classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('sidebar-open');
        }

        // ── Profile dropdown ──
        function toggleDropdown() {
            const dd = document.getElementById('profile-dropdown');
            const btn = document.getElementById('profile-btn');
            dd.classList.toggle('hidden');
            btn.classList.toggle('open');
        }

        document.addEventListener('click', function(e) {
            const btn = document.getElementById('profile-btn');
            const dd = document.getElementById('profile-dropdown');
            if (dd && btn && !btn.contains(e.target) && !dd.contains(e.target)) {
                dd.classList.add('hidden');
                btn.classList.remove('open');
            }
        });

        // ── Resize handler ──
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                document.body.classList.remove('sidebar-open');
                const overlay = document.getElementById('sidebar-overlay');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100');
                // Restore sidebar visibility for desktop
                document.getElementById('sidebar').classList.remove('-translate-x-full');
            }

            // Recalculate open group heights on resize (mis. rotasi layar)
            document.querySelectorAll('.nav-group.open .nav-group-content').forEach(content => {
                content.style.maxHeight = content.scrollHeight + 'px';
            });
        });
    </script>
    @stack('scripts')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>

</html>