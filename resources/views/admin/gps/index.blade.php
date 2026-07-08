@extends('admin.layouts.app')

@section('title', 'Data GPS')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data GPS</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola perangkat GPS kendaraan</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah GPS
        </button>
    </div>


    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar GPS</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total perangkat</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari GPS..."
                        oninput="filterGpsTable(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-download text-xs"></i> Export
                </button>
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pengguna</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama GPS</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Alamat</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Marketing</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kontak</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bengkel</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kontak</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="gpsTableBody">
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                            data-search="{{ strtolower($d->nama_gps . ' ' . $d->alamat . ' ' . $d->nama_marketing . ' ' . $d->nama_bengkel) }}">

                            {{-- No --}}
                            <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($d->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $d->user->name ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-map-marker-alt text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $d->nama_gps }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">{{ $d->alamat }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->nama_marketing }}</td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->kontak_marketing }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->nama_bengkel }}</td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->kontak_bengkel }}</span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-nama_gps="{{ $d->nama_gps }}"
                                        data-alamat="{{ $d->alamat }}"
                                        data-nama_marketing="{{ $d->nama_marketing }}"
                                        data-kontak_marketing="{{ $d->kontak_marketing }}"
                                        data-nama_bengkel="{{ $d->nama_bengkel }}"
                                        data-kontak_bengkel="{{ $d->kontak_bengkel }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="/admin/gps/{{ $d->id }}" method="POST"
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
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-map-marker-alt text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data GPS</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah GPS" untuk menambahkan perangkat baru</p>
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
    MODAL TAMBAH / EDIT GPS
======================================--}}
<div id="gpsModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah GPS</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data perangkat GPS kendaraan</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="gpsForm" action="/admin/gps" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama GPS <span class="text-red-500">*</span></label>
                <input type="text" name="nama_gps" id="f_nama_gps" required
                    placeholder="Contoh: GT06N, Teltonika FMB920"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                <textarea name="alamat" id="f_alamat" rows="3" required
                    placeholder="Masukkan alamat lengkap..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Marketing <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_marketing" id="f_nama_marketing" required
                        placeholder="Nama marketing"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Marketing <span class="text-red-500">*</span></label>
                    <input type="text" name="kontak_marketing" id="f_kontak_marketing" required
                        placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bengkel <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_bengkel" id="f_nama_bengkel" required
                        placeholder="Nama bengkel"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Bengkel <span class="text-red-500">*</span></label>
                    <input type="text" name="kontak_bengkel" id="f_kontak_bengkel" required
                        placeholder="08xx-xxxx-xxxx"
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
    POPUP ALERT (FIXED OVERLAY — seperti dashboard)
======================================--}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">

        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
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
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
// ── GPS MODAL ──────────────────────────────────────
const gpsModal        = document.getElementById('gpsModal');
const gpsForm         = document.getElementById('gpsForm');
const methodContainer = document.getElementById('methodContainer');

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah GPS';
    gpsForm.action = '/admin/gps';
    methodContainer.innerHTML = '';
    gpsForm.reset();
    gpsModal.classList.remove('hidden');
    gpsModal.classList.add('flex');
}

function closeModal() {
    gpsModal.classList.add('hidden');
    gpsModal.classList.remove('flex');
}

gpsModal.addEventListener('click', function(e) {
    if (e.target === gpsModal) closeModal();
});

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit GPS';
    gpsForm.action = '/admin/gps/' + btn.dataset.id;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_nama_gps').value         = btn.dataset.nama_gps;
    document.getElementById('f_alamat').value            = btn.dataset.alamat;
    document.getElementById('f_nama_marketing').value   = btn.dataset.nama_marketing;
    document.getElementById('f_kontak_marketing').value = btn.dataset.kontak_marketing;
    document.getElementById('f_nama_bengkel').value     = btn.dataset.nama_bengkel;
    document.getElementById('f_kontak_bengkel').value   = btn.dataset.kontak_bengkel;
    gpsModal.classList.remove('hidden');
    gpsModal.classList.add('flex');
}

function filterGpsTable(q) {
    document.querySelectorAll('#gpsTableBody tr[data-search]').forEach(row => {
        row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
    });
}

// ── POPUP ALERT (fixed overlay) ────────────────────
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function () {
        overlay.style.opacity      = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform        = 'translateY(0)';
    }, 80);

    var timer = setTimeout(closeAlert, 4500);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeAlert();
    });

    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity      = '0';
        overlay.style.pointerEvents = 'none';
        box.style.transform        = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

@endsection