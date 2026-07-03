@extends('admin.layouts.app')

@section('title', 'Supplier')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Supplier</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data supplier barang</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="bi bi-plus-lg text-sm"></i>
                Tambah Supplier
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            {{-- Total Supplier --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Supplier</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $data->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-people-fill text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Nominal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Nominal</p>
                        <h2 class="text-2xl font-bold text-green-600 mt-2">
                            Rp {{ number_format($data->sum(fn($d) => $d->jumlah_barang * $d->harga_barang)) }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Barang --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Jumlah Barang</p>
                        <h2 class="text-3xl font-bold text-orange-500 mt-2">{{ $data->sum('jumlah_barang') }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center">
                        <i class="bi bi-box-seam-fill text-2xl text-orange-500"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Supplier</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total supplier</p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="pdfBtn" target="_blank" href="{{ route('supplier.export.pdf') }}"
                        class="px-3 py-1.5 text-xs bg-red-500 text-white rounded-lg">
                        Export PDF
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari supplier..." oninput="filterSupplierTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">User
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Supplier</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                                Telp</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Barang</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jumlah</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Harga</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Total</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTableBody">
                        @forelse ($data as $i => $d)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($d->nama_supplier . ' ' . $d->no_telp . ' ' . $d->nama_barang . ' ' . ($d->user->name ?? '')) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $i + 1 }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($d->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $d->user->name ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="bi bi-person-lines-fill text-blue-400 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $d->nama_supplier }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_telp }}</span>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->nama_barang }}</td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->jumlah_barang }}</td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">Rp {{ number_format($d->harga_barang) }}</td>

                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-green-600">
                                        Rp {{ number_format($d->jumlah_barang * $d->harga_barang) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            data-id="{{ $d->id }}" data-user_id="{{ $d->user_id }}"
                                            data-nama_supplier="{{ $d->nama_supplier }}"
                                            data-no_telp="{{ $d->no_telp }}" data-nama_barang="{{ $d->nama_barang }}"
                                            data-jumlah_barang="{{ $d->jumlah_barang }}"
                                            data-harga_barang="{{ $d->harga_barang }}" onclick="triggerEdit(this)">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="/admin/supplier/{{ $d->id }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="fa fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-people-fill text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data supplier</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Supplier" untuk menambahkan data baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH / EDIT SUPPLIER
====================================== --}}
    <div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Supplier</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data supplier dengan lengkap</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="supplierForm" action="/admin/supplier" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div id="methodContainer"></div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Supplier <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_supplier" id="f_nama_supplier" required
                        placeholder="Contoh: CV Maju Jaya"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Telp <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_telp" id="f_no_telp" required placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Barang <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_barang" id="f_nama_barang" required
                        placeholder="Contoh: Kabel HDMI 2m"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Barang <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_barang" id="f_jumlah_barang" required min="1"
                            placeholder="0"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Barang <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="harga_barang" id="f_harga_barang" required min="0"
                            placeholder="0"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan Data
                </button>
            </form>

        </div>
    </div>


    {{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
====================================== --}}
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
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif


    {{-- STYLE & SCRIPT --}}
    <style>
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

    <script>
        // ── SUPPLIER MODAL ──────────────────────────────────────
        const supplierModal = document.getElementById('supplierModal');
        const supplierForm = document.getElementById('supplierForm');
        const methodContainer = document.getElementById('methodContainer');

        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Supplier';
            supplierForm.action = '/admin/supplier';
            methodContainer.innerHTML = '';
            supplierForm.reset();
            supplierModal.classList.remove('hidden');
            supplierModal.classList.add('flex');
        }

        function closeModal() {
            supplierModal.classList.add('hidden');
            supplierModal.classList.remove('flex');
        }

        supplierModal.addEventListener('click', function(e) {
            if (e.target === supplierModal) closeModal();
        });

        function triggerEdit(btn) {
            document.getElementById('modalTitle').innerText = 'Edit Supplier';
            supplierForm.action = '/admin/supplier/' + btn.dataset.id;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('f_nama_supplier').value = btn.dataset.nama_supplier;
            document.getElementById('f_no_telp').value = btn.dataset.no_telp;
            document.getElementById('f_nama_barang').value = btn.dataset.nama_barang;
            document.getElementById('f_jumlah_barang').value = btn.dataset.jumlah_barang;
            document.getElementById('f_harga_barang').value = btn.dataset.harga_barang;
            supplierModal.classList.remove('hidden');
            supplierModal.classList.add('flex');
        }

        function filterSupplierTable(q) {
            document.querySelectorAll('#supplierTableBody tr[data-search]').forEach(row => {
                row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
            });

            // update link PDF
            document.getElementById('pdfBtn').href = '/admin/supplier/pdf?search=' + encodeURIComponent(q);
        }

        // ── POPUP ALERT (fixed overlay) ────────────────────
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

@endsection
