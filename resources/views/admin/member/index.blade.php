@extends('admin.layouts.app')

@section('title', 'Member')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Member</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data member pelanggan</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Member
            </button>
        </div>



        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Member</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total member</p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="pdfBtn" target="_blank" href="/admin/member/pdf"
                        class="px-3 py-1.5 text-xs bg-red-500 text-white rounded-lg">
                        Export PDF
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari member..." oninput="filterMemberTable(this.value)"
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
                                Jenis Member</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Alamat</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="memberTableBody">
                        @forelse ($data as $i => $d)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($d->nama_member . ' ' . $d->kontak_member . ' ' . $d->alamat) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $i + 1 }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($d->nama_member, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $d->nama_member }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 px-2 py-0.5 rounded">{{ $d->kontak_member }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 px-2 py-0.5 rounded">{{ $d->email_member }}</span>
                                </td>

                                <td class="px-4 py-3.5">

                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                    {{ $d->jenis_member == 'perorangan' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                        {{ ucfirst($d->jenis_member) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[200px] truncate">{{ $d->alamat ?? '-' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            data-id="{{ $d->id }}" data-nama_member="{{ $d->nama_member }}"
                                            data-kontak_member="{{ $d->kontak_member }}"
                                            data-email_member="{{ $d->email_member }}" data-alamat="{{ $d->alamat }}"
                                            data-jenis_member="{{ $d->jenis_member }}" onclick="triggerEdit(this)">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="/admin/member/{{ $d->id }}" method="POST"
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
                                        <p class="text-sm font-medium text-gray-500">Belum ada data member</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Member" untuk menambahkan data baru
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
    MODAL TAMBAH / EDIT MEMBER
====================================== --}}
    <div id="memberModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Member</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data member dengan lengkap</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="memberForm" action="/admin/member" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div id="methodContainer"></div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Member <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_member" id="f_nama_member" required placeholder="Contoh: Budi Santoso"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="kontak_member" id="f_kontak_member" placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="email_member" id="f_email_member" placeholder="xxxx@xxxx.xxx"
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
                        Jenis Member <span class="text-red-500">*</span>
                    </label>

                    <select name="jenis_member" id="f_jenis_member"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        required>
                        <option value="">-- Pilih Jenis Member --</option>
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
        // ── MEMBER MODAL ──────────────────────────────────────
        const memberModal = document.getElementById('memberModal');
        const memberForm = document.getElementById('memberForm');
        const methodContainer = document.getElementById('methodContainer');

        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Member';
            memberForm.action = '/admin/member';
            methodContainer.innerHTML = '';
            memberForm.reset();
            memberModal.classList.remove('hidden');
            memberModal.classList.add('flex');
        }

        function closeModal() {
            memberModal.classList.add('hidden');
            memberModal.classList.remove('flex');
        }

        memberModal.addEventListener('click', function(e) {
            if (e.target === memberModal) closeModal();
        });

        function triggerEdit(btn) {
            document.getElementById('modalTitle').innerText = 'Edit Member';
            memberForm.action = '/admin/member/' + btn.dataset.id;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('f_nama_member').value = btn.dataset.nama_member;
            document.getElementById('f_kontak_member').value = btn.dataset.kontak_member;
            document.getElementById('f_email_member').value = btn.dataset.email_member;
            document.getElementById('f_jenis_member').value = btn.dataset.jenis_member;
            document.getElementById('f_alamat').value = btn.dataset.alamat;
            memberModal.classList.remove('hidden');
            memberModal.classList.add('flex');
        }

        // ── SEARCH + SHOW ENTRIES ────────────────────────────
        const allRows    = Array.from(document.querySelectorAll('#memberTableBody tr[data-search]'));
        let currentSearch = '';

        function filterMemberTable(q) {
            currentSearch = q.toLowerCase();
            document.getElementById('pdfBtn').href =
                '/admin/member/pdf?search=' + encodeURIComponent(q);
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
