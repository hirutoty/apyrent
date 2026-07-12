@extends('admin.layouts.app')

@section('title', 'Pelanggan')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Pelanggan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data pelanggan</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Pelanggan
            </button>
        </div>



        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Pelanggan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total pelanggan</p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="pdfBtn" target="_blank" href="/admin/pelanggan/pdf"
                        class="px-3 py-1.5 text-xs bg-red-500 text-white rounded-lg">
                        Export PDF
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari pelanggan..." oninput="filterPelangganTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            {{-- SHOW ENTRIES --}}
            <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
                <span>Show</span>
                <select id="perPageSelect" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">All</option>
                </select>
                <span>entries</span>
                <div class="ml-auto text-xs text-gray-400" id="entriesInfoTop"></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kontak</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Email</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jenis Pelanggan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Alamat</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pelangganTableBody">
                        @forelse ($data as $i => $d)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($d->nama_pelanggan . ' ' . $d->kontak_pelanggan . ' ' . $d->alamat) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $i + 1 }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($d->nama_pelanggan, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $d->nama_pelanggan }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 px-2 py-0.5 rounded">{{ $d->kontak_pelanggan }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 px-2 py-0.5 rounded">{{ $d->email_pelanggan }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full
                                    {{ $d->jenis_pelanggan == 'perorangan' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                        {{ ucfirst($d->jenis_pelanggan) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[200px] truncate">{{ $d->alamat ?? '-' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            data-id="{{ $d->id }}" data-nama_pelanggan="{{ $d->nama_pelanggan }}"
                                            data-kontak_pelanggan="{{ $d->kontak_pelanggan }}"
                                            data-email_pelanggan="{{ $d->email_pelanggan }}" data-alamat="{{ $d->alamat }}"
                                            data-jenis_pelanggan="{{ $d->jenis_pelanggan }}" onclick="triggerEdit(this)">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="/admin/pelanggan/{{ $d->id }}" method="POST"
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
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-person-check-fill text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data pelanggan</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Pelanggan" untuk menambahkan data baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH / EDIT PELANGGAN
====================================== --}}
    <div id="pelangganModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Pelanggan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data pelanggan dengan lengkap</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="pelangganForm" action="/admin/pelanggan" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div id="methodContainer"></div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pelanggan <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_pelanggan" id="f_nama_pelanggan" required placeholder="Contoh: Budi Santoso"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="kontak_pelanggan" id="f_kontak_pelanggan" placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="email_pelanggan" id="f_email_pelanggan" placeholder="xxxx@xxxx.xxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat <span
                            class="text-red-500">*</span></label>
                    <textarea name="alamat" id="f_alamat" rows="3" placeholder="Masukkan alamat lengkap..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Jenis Pelanggan <span class="text-red-500">*</span>
                    </label>

                    <select name="jenis_pelanggan" id="f_jenis_pelanggan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        required>
                        <option value="">-- Pilih Jenis Pelanggan --</option>
                        <option value="perorangan">Perorangan</option>
                        <option value="perusahaan">Perusahaan</option>
                    </select>
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
        // ── PELANGGAN MODAL ──────────────────────────────────────
        const pelangganModal = document.getElementById('pelangganModal');
        const pelangganForm = document.getElementById('pelangganForm');
        const methodContainer = document.getElementById('methodContainer');

        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
            pelangganForm.action = '/admin/pelanggan';
            methodContainer.innerHTML = '';
            pelangganForm.reset();
            pelangganModal.classList.remove('hidden');
            pelangganModal.classList.add('flex');
        }

        function closeModal() {
            pelangganModal.classList.add('hidden');
            pelangganModal.classList.remove('flex');
        }

        pelangganModal.addEventListener('click', function(e) {
            if (e.target === pelangganModal) closeModal();
        });

        function triggerEdit(btn) {
            document.getElementById('modalTitle').innerText = 'Edit Pelanggan';
            pelangganForm.action = '/admin/pelanggan/' + btn.dataset.id;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('f_nama_pelanggan').value = btn.dataset.nama_pelanggan;
            document.getElementById('f_kontak_pelanggan').value = btn.dataset.kontak_pelanggan;
            document.getElementById('f_email_pelanggan').value = btn.dataset.email_pelanggan;
            document.getElementById('f_jenis_pelanggan').value = btn.dataset.jenis_pelanggan;
            document.getElementById('f_alamat').value = btn.dataset.alamat;
            pelangganModal.classList.remove('hidden');
            pelangganModal.classList.add('flex');
        }

        // ── SEARCH + SHOW ENTRIES ────────────────────────────
        const allRows    = Array.from(document.querySelectorAll('#pelangganTableBody tr[data-search]'));
        let currentSearch = '';

        function filterPelangganTable(q) {
            currentSearch = q.toLowerCase();
            document.getElementById('pdfBtn').href =
                '/admin/pelanggan/pdf?search=' + encodeURIComponent(q);
            renderTable();
        }

        function renderTable() {
            const perPageEl = document.getElementById('perPageSelect');
            const perPage   = perPageEl.value === 'all' ? Infinity : parseInt(perPageEl.value, 10);

            const matched = allRows.filter(row => row.dataset.search.includes(currentSearch));
            let shown = 0;

            allRows.forEach(row => row.style.display = 'none');
            matched.forEach(row => {
                if (shown < perPage) {
                    row.style.display = '';
                    shown++;
                }
            });

            const infoText = matched.length === 0
                ? 'Tidak ada data yang cocok'
                : `Menampilkan ${shown} dari ${matched.length} entri` +
                  (currentSearch ? ' (hasil pencarian)' : '');

            const top = document.getElementById('entriesInfoTop');
            const bot = document.getElementById('entriesInfo');
            if (top) top.innerText = infoText;
            if (bot) bot.innerText = infoText;
        }

        document.addEventListener('DOMContentLoaded', renderTable);

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
