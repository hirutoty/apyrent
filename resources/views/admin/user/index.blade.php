@extends('admin.layouts.app')

@section('title', 'User')

@push('styles')
    <style>
        /* ── Role select colors ── */
        select.role-select {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            padding-right: 1.6rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23fff' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 10px;
            transition: opacity 150ms ease;
        }

        select.role-select:hover {
            opacity: 0.88;
        }

        select.role-select.role-superadmin {
            background-color: #ef4444;
            color: #fff;
        }

        select.role-select.role-keuangan {
            background-color: #3b82f6;
            color: #fff;
        }

        select.role-select.role-produksi {
            background-color: #f59e0b;
            color: #fff;
        }

        select.role-select.role-hrd {
            background-color: #8b5cf6;
            color: #fff;
        }

        select.role-select.role-purchase {
            background-color: #10b981;
            color: #fff;
        }

        select.role-select.role-sales {
            background-color: #f97316;
            color: #fff;
        }

        select.role-select.role-marketing {
            background-color: #ec4899;
            color: #fff;
        }

        select.role-select.role-it {
            background-color: #06b6d4;
            color: #fff;
        }

        /* ── Status select colors ── */
        select.status-select {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            padding-right: 1.6rem;
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 10px;
            transition: opacity 150ms ease;
        }

        select.status-select:hover {
            opacity: 0.88;
        }

        select.status-select.status-aktif {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2316a34a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        }

        select.status-select.status-blokir {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23dc2626' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data User</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola semua data pengguna sistem</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah User
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 xl:grid-cols-3 gap-4">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Total User</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-1.5">{{ $data->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-people-fill text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Superadmin</p>
                        <h2 class="text-3xl font-bold text-red-500 mt-1.5">{{ $data->where('role', 'superadmin')->count() }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                        <i class="bi bi-shield-lock-fill text-xl text-red-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Keuangan</p>
                        <h2 class="text-3xl font-bold text-blue-500 mt-1.5">{{ $data->where('role', 'keuangan')->count() }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Produksi</p>
                        <h2 class="text-3xl font-bold text-amber-500 mt-1.5">{{ $data->where('role', 'produksi')->count() }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                        <i class="bi bi-gear-fill text-xl text-amber-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">HRD</p>
                        <h2 class="text-3xl font-bold mt-1.5" style="color:#8b5cf6">{{ $data->where('role', 'hrd')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#f5f3ff">
                        <i class="bi bi-people-fill text-xl" style="color:#8b5cf6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Purchase</p>
                        <h2 class="text-3xl font-bold mt-1.5" style="color:#10b981">{{ $data->where('role', 'purchase')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#ecfdf5">
                        <i class="bi bi-cart-fill text-xl" style="color:#10b981"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Sales</p>
                        <h2 class="text-3xl font-bold mt-1.5" style="color:#f97316">{{ $data->where('role', 'sales')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#fff7ed">
                        <i class="bi bi-cart-check-fill text-xl" style="color:#f97316"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Marketing</p>
                        <h2 class="text-3xl font-bold mt-1.5" style="color:#ec4899">{{ $data->where('role', 'marketing')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#fdf2f8">
                        <i class="bi bi-megaphone-fill text-xl" style="color:#ec4899"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">IT</p>
                        <h2 class="text-3xl font-bold mt-1.5" style="color:#06b6d4">{{ $data->where('role', 'it')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#ecfeff">
                        <i class="bi bi-cpu text-xl" style="color:#06b6d4"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">User Aktif</p>
                        <h2 class="text-3xl font-bold text-emerald-600 mt-1.5">
                            {{ $data->where('status', 'aktif')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                        <i class="bi bi-check-circle-fill text-xl text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">User Blokir</p>
                        <h2 class="text-3xl font-bold text-gray-600 mt-1.5">{{ $data->where('status', 'blokir')->count() }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center">
                        <i class="bi bi-slash-circle-fill text-xl text-gray-500"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 px-5 py-4 border-b border-gray-100">

                {{-- Info --}}
                <div>
                    <h2 class="font-bold text-gray-800 text-[15px]">
                        Daftar User
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $data->count() }} total pengguna terdaftar
                    </p>
                </div>

                {{-- Action --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">

                    {{-- Search --}}
                    <div class="relative">
                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>

                        <input type="text" placeholder="Cari user..." oninput="filterUserTable(this.value)"
                            class="pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-xl
                       focus:outline-none focus:ring-2 focus:ring-blue-100
                       focus:border-blue-400 w-full sm:w-56 transition-all">
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center gap-2">

                        <a id="pdfBtn" target="_blank" href="{{ route('user.export.pdf') }}"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium
                       bg-red-500 text-white rounded-xl hover:bg-red-600 transition">

                            <i class="fa fa-file-pdf"></i>
                            Export PDF
                        </a>

                        <button onclick="window.location.reload()"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium
                       text-gray-600 border border-gray-200 rounded-xl
                       hover:bg-gray-50 transition-colors">

                            <i class="fa fa-rotate-right"></i>
                            Refresh
                        </button>

                    </div>

                </div>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th
                                class="text-left text-[11px] font-bold uppercase tracking-widest text-gray-400 px-4 py-3 w-10">
                                No</th>
                            <th class="text-left text-[11px] font-bold uppercase tracking-widest text-gray-400 px-4 py-3">
                                User</th>
                            <th class="text-left text-[11px] font-bold uppercase tracking-widest text-gray-400 px-4 py-3">
                                Kontak</th>
                            <th class="text-left text-[11px] font-bold uppercase tracking-widest text-gray-400 px-4 py-3">
                                Role</th>
                            <th class="text-left text-[11px] font-bold uppercase tracking-widest text-gray-400 px-4 py-3">
                                Status</th>
                            <th class="text-center text-[11px] font-bold uppercase tracking-widest text-gray-400 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        @forelse ($data as $i => $d)
                            <tr class="border-t border-gray-50 hover:bg-blue-50/30 transition-colors duration-100 group"
                                data-search="{{ strtolower($d->name . ' ' . $d->username . ' ' . $d->email . ' ' . $d->no_telp . ' ' . $d->role) }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $i + 1 }}</td>

                                {{-- User --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if ($d->foto)
                                           <img src="{{ asset('storage/' . $d->foto) }}"
                                                class="w-9 h-9 rounded-full object-cover border-2 border-gray-100 flex-shrink-0">
                                        @else
                                            <div
                                                class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 text-white
                                            {{ $d->role == 'superadmin'
                                                ? 'bg-gradient-to-br from-red-400 to-red-600'
                                                : ($d->role == 'keuangan'
                                                    ? 'bg-gradient-to-br from-blue-400 to-blue-600'
                                                    : ($d->role == 'produksi'
                                                        ? 'bg-gradient-to-br from-amber-400 to-amber-600'
                                                        : ($d->role == 'hrd'
                                                            ? 'bg-gradient-to-br from-violet-400 to-violet-600'
                                                            : ($d->role == 'purchase'
                                                                ? 'bg-gradient-to-br from-emerald-400 to-emerald-600'
                                                                : ($d->role == 'sales'
                                                                    ? 'bg-gradient-to-br from-orange-400 to-orange-600'
                                                                    : ($d->role == 'marketing'
                                                                        ? 'bg-gradient-to-br from-pink-400 to-pink-600'
                                                                        : 'bg-gradient-to-br from-cyan-400 to-cyan-600')))))) }}">
                                                {{ strtoupper(substr($d->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-[13px] font-semibold text-gray-800">{{ $d->name }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $d->username }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kontak --}}
                                <td class="px-4 py-3.5">
                                    <p class="text-[13px] text-gray-700">{{ $d->email }}</p>
                                    <span
                                        class="font-mono text-[11px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md mt-0.5 inline-block">{{ $d->no_telp }}</span>
                                </td>

                                {{-- Role — independent color per row ── --}}
                                <td class="px-4 py-3.5">
                                    <form action="/admin/user/{{ $d->id }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $d->name }}">
                                        <input type="hidden" name="username" value="{{ $d->username }}">
                                        <input type="hidden" name="email" value="{{ $d->email }}">
                                        <input type="hidden" name="no_telp" value="{{ $d->no_telp }}">
                                        <input type="hidden" name="status" value="{{ $d->status }}">

                                        <select name="role" onchange="updateRoleColor(this); this.form.submit()"
                                            class="role-select role-{{ $d->role }} text-xs font-semibold px-2.5 py-1.5 rounded-lg border-0 outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-300">
                                            <option value="superadmin" {{ $d->role == 'superadmin' ? 'selected' : '' }}>
                                                Superadmin</option>
                                            <option value="keuangan" {{ $d->role == 'keuangan' ? 'selected' : '' }}>
                                                Keuangan</option>
                                            <option value="produksi" {{ $d->role == 'produksi' ? 'selected' : '' }}>
                                                Produksi</option>
                                            <option value="hrd" {{ $d->role == 'hrd' ? 'selected' : '' }}>
                                                HRD</option>
                                            <option value="purchase" {{ $d->role == 'purchase' ? 'selected' : '' }}>
                                                Purchase</option>
                                            <option value="sales" {{ $d->role == 'sales' ? 'selected' : '' }}>
                                                Sales</option>
                                            <option value="marketing" {{ $d->role == 'marketing' ? 'selected' : '' }}>
                                                Marketing</option>
                                            <option value="it" {{ $d->role == 'it' ? 'selected' : '' }}>
                                                IT</option>
                                        </select>
                                    </form>
                                </td>

                                {{-- Status — independent color per row ── --}}
                                <td class="px-4 py-3.5">
                                    <form action="/admin/user/{{ $d->id }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $d->name }}">
                                        <input type="hidden" name="username" value="{{ $d->username }}">
                                        <input type="hidden" name="email" value="{{ $d->email }}">
                                        <input type="hidden" name="no_telp" value="{{ $d->no_telp }}">
                                        <input type="hidden" name="role" value="{{ $d->role }}">

                                        <select name="status" onchange="updateStatusColor(this); this.form.submit()"
                                            class="status-select status-{{ $d->status }} text-xs font-semibold px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-300">
                                            <option value="aktif" {{ $d->status == 'aktif' ? 'selected' : '' }}>✓ Aktif
                                            </option>
                                            <option value="blokir" {{ $d->status == 'blokir' ? 'selected' : '' }}>✕ Blokir
                                            </option>
                                        </select>
                                    </form>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            class="btn-edit inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 hover:border-amber-300 transition-all duration-150"
                                            data-id="{{ $d->id }}" data-name="{{ $d->name }}"
                                            data-username="{{ $d->username }}" data-email="{{ $d->email }}"
                                            data-no_telp="{{ $d->no_telp }}" data-foto="{{ $d->foto }}"
                                            data-role="{{ $d->role }}" data-status="{{ $d->status }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form action="/admin/user/{{ $d->id }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all duration-150">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-people-fill text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500">Belum ada data user</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah User" untuk menambahkan pengguna baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH / EDIT USER
====================================== --}}
    <div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 overflow-y-auto py-6"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 id="modalTitle" class="text-[15px] font-bold text-gray-800">Tambah User</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Isi semua field yang diperlukan</p>
                </div>
                <button onclick="closeModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i class="fa fa-times text-sm"></i>
                </button>
            </div>

            <form id="userForm" action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data"
                class="px-6 py-5">
                @csrf
                <div id="methodContainer"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="f_name" required placeholder="Nama lengkap"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="username" id="f_username" required placeholder="Username unik"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="f_email" required placeholder="email@domain.com"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="f_password" required
                                placeholder="Minimal 6 karakter"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all pr-10">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i id="eyeIcon" class="fa fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Telp <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="no_telp" id="f_no_telp" required placeholder="08xx-xxxx-xxxx"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="file" name="foto" id="f_foto" accept="image/jpg,image/jpeg,image/png,image/webp"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                        <img id="fotoPreview" src="" alt="Preview" class="hidden mt-2 w-16 h-16 rounded-xl object-cover border border-gray-200">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role" id="f_role" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                            <option value="superadmin">Superadmin</option>
                            <option value="keuangan">Keuangan</option>
                            <option value="produksi">Produksi</option>
                            <option value="hrd">HRD</option>
                            <option value="purchase">Purchase</option>
                            <option value="sales">Sales</option>
                            <option value="marketing">Marketing</option>
                            <option value="it">IT</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="f_status"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                            <option value="aktif">Aktif</option>
                            <option value="blokir">Blokir</option>
                        </select>
                    </div>

                </div>

                <button type="submit"
                    class="mt-5 w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold py-3 rounded-xl transition-all duration-150 flex items-center justify-center gap-2 shadow-sm shadow-blue-200">
                    <i class="fa fa-save"></i> Simpan Data
                </button>
            </form>

        </div>
    </div>


    {{-- ======================================
    POPUP ALERT
====================================== --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
            <div id="alertBox"
                class="bg-white rounded-2xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">
                @if (session('success'))
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
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
                    class="text-gray-300 hover:text-gray-600 transition-colors text-lg leading-none flex-shrink-0">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    @endif


    @push('scripts')
        <script>
            // ══════════════════════════════════════════════
            //  ROLE & STATUS COLOR — per row, independent
            // ══════════════════════════════════════════════

            // Role color classes map
            var roleClasses = {
                superadmin: 'role-superadmin',
                keuangan: 'role-keuangan',
                produksi: 'role-produksi',
                hrd: 'role-hrd',
                purchase: 'role-purchase',
                sales: 'role-sales',
                marketing: 'role-marketing',
                it: 'role-it'
            };
            var statusClasses = {
                aktif: 'status-aktif',
                blokir: 'status-blokir'
            };

            /**
             * Called onchange on a role <select>.
             * Removes all role-* classes then adds the correct one for THIS select only.
             */
            function updateRoleColor(sel) {
                Object.values(roleClasses).forEach(function(c) {
                    sel.classList.remove(c);
                });
                sel.classList.add(roleClasses[sel.value] || '');
            }

            /**
             * Called onchange on a status <select>.
             * Removes all status-* classes then adds the correct one for THIS select only.
             */
            function updateStatusColor(sel) {
                Object.values(statusClasses).forEach(function(c) {
                    sel.classList.remove(c);
                });
                sel.classList.add(statusClasses[sel.value] || '');
            }

            // ── On page load: apply correct colors to every row independently ──
            document.querySelectorAll('select.role-select').forEach(function(sel) {
                // Already set via PHP class, but re-apply to be safe
                Object.values(roleClasses).forEach(function(c) {
                    sel.classList.remove(c);
                });
                sel.classList.add(roleClasses[sel.value] || '');
            });

            document.querySelectorAll('select.status-select').forEach(function(sel) {
                Object.values(statusClasses).forEach(function(c) {
                    sel.classList.remove(c);
                });
                sel.classList.add(statusClasses[sel.value] || '');
            });

            // ══════════════════════════════════════════════
            //  MODAL
            // ══════════════════════════════════════════════
            var userModal = document.getElementById('userModal');
            var userForm = document.getElementById('userForm');
            var methodContainer = document.getElementById('methodContainer');

            function openModal() {
                document.getElementById('modalTitle').innerText = 'Tambah User';
                userForm.action = '{{ route('user.store') }}';
                methodContainer.innerHTML = '';
                userForm.reset();
                document.getElementById('fotoPreview').classList.add('hidden');
                document.getElementById('fotoPreview').src = '';
                document.getElementById('f_password').setAttribute('required', '');
                userModal.classList.remove('hidden');
                userModal.classList.add('flex');
            }

            function closeModal() {
                userModal.classList.add('hidden');
                userModal.classList.remove('flex');
            }

            userModal.addEventListener('click', function(e) {
                if (e.target === userModal) closeModal();
            });

            document.querySelectorAll('.btn-edit').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('modalTitle').innerText = 'Edit User';
                    userForm.action = '/admin/user/' + this.dataset.id;
                    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                    document.getElementById('f_name').value = this.dataset.name;
                    document.getElementById('f_username').value = this.dataset.username;
                    document.getElementById('f_email').value = this.dataset.email;
                    document.getElementById('f_no_telp').value = this.dataset.no_telp;
                    document.getElementById('f_role').value = this.dataset.role;
                    document.getElementById('f_status').value = this.dataset.status;
                    document.getElementById('f_password').removeAttribute('required');
                    document.getElementById('f_password').value = '';

                    // Tampilkan foto existing jika ada
                    var preview = document.getElementById('fotoPreview');
                    if (this.dataset.foto) {
                        preview.src = '/storage/' + this.dataset.foto;
                        preview.classList.remove('hidden');
                    } else {
                        preview.src = '';
                        preview.classList.add('hidden');
                    }

                    userModal.classList.remove('hidden');
                    userModal.classList.add('flex');
                });
            });

            // ── Toggle password visibility ──
            function togglePassword() {
                var inp = document.getElementById('f_password');
                var ico = document.getElementById('eyeIcon');
                if (inp.type === 'password') {
                    inp.type = 'text';
                    ico.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    inp.type = 'password';
                    ico.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }

            // ── Preview foto saat dipilih ──
            document.getElementById('f_foto').addEventListener('change', function() {
                var preview = document.getElementById('fotoPreview');
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    preview.classList.add('hidden');
                    preview.src = '';
                }
            });

            // ── Table search filter ──
            function filterUserTable(q) {
                document.querySelectorAll('#userTableBody tr[data-search]').forEach(row => {
                    row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
                });

                // update PDF link ikut filter
                document.getElementById('pdfBtn').href =
                    '/admin/user/export-pdf?search=' + encodeURIComponent(q);
            }

            // ══════════════════════════════════════════════
            //  POPUP ALERT
            // ══════════════════════════════════════════════
            (function() {
                var overlay = document.getElementById('alertOverlay');
                var box = document.getElementById('alertBox');
                if (!overlay) return;

                setTimeout(function() {
                    overlay.style.opacity = '1';
                    overlay.style.pointerEvents = 'auto';
                    box.style.transform = 'translateY(0)';
                }, 80);

                var timer = setTimeout(closeAlert, 4500);
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) closeAlert();
                });

                function closeAlert() {
                    clearTimeout(timer);
                    overlay.style.opacity = '0';
                    overlay.style.pointerEvents = 'none';
                    box.style.transform = 'translateY(-16px)';
                }
                window.closeAlert = closeAlert;
            })();
        </script>
    @endpush

@endsection

{{-- DB_DATABASE=sofw9743_apyrent2
DB_USERNAME=sofw9743_apydev
DB_PASSWORD=bM?&!KYym3-}0=HC --}}
