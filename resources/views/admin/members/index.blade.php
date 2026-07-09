@extends('admin.layouts.app')

@section('title', 'Member')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Member</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data mitra pemilik kendaraan</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus text-sm"></i> Tambah Member
        </button>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Member</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} total member</p>
            </div>
            <div class="flex items-center gap-2">
                <a id="pdfBtn" target="_blank" href="{{ route('members.pdf') }}"
                    class="px-3 py-1.5 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fa fa-file-pdf mr-1"></i> Export PDF
                </a>
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari member..." oninput="filterTable(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-sync text-xs"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span>
            <select id="perPageSelect" onchange="renderTable()"
                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kontak</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Email</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jenis</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dokumen</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="memberTableBody">
                    @forelse ($data as $i => $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                            data-search="{{ strtolower($d->nama . ' ' . $d->kontak . ' ' . $d->email . ' ' . $d->alamat) }}">

                            <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($d->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $d->nama }}</p>
                                        <p class="text-xs text-gray-400">{{ $d->alamat ? Str::limit($d->alamat, 30) : '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-xs text-gray-600 font-mono">{{ $d->kontak ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-xs text-gray-600">{{ $d->email ?? '-' }}</td>

                            <td class="px-4 py-3.5">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    {{ $d->jenis_member == 'perorangan' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                    {{ ucfirst($d->jenis_member) }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">
                                    <i class="fa fa-car text-xs"></i> {{ $d->kendaraans_count }} unit
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($d->file_stnk)
                                        @foreach($d->file_stnk as $stnkIdx => $stnkPath)
                                        <a href="{{ asset($stnkPath) }}" target="_blank"
                                            class="text-xs px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded font-medium">STNK{{ count($d->file_stnk) > 1 ? ' '.($stnkIdx+1) : '' }}</a>
                                        @endforeach
                                    @endif
                                    @if($d->file_attachment)
                                        @foreach($d->file_attachment as $attIdx => $attPath)
                                        <a href="{{ asset($attPath) }}" target="_blank"
                                            class="text-xs px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded font-medium">Att{{ count($d->file_attachment) > 1 ? ' '.($attIdx+1) : '' }}</a>
                                        @endforeach
                                    @endif
                                    @if($d->file_kontrak)
                                        <a href="{{ asset($d->file_kontrak) }}" target="_blank"
                                            class="text-xs px-1.5 py-0.5 bg-teal-100 text-teal-700 rounded font-medium">Kontrak</a>
                                    @endif
                                    @if(!$d->file_stnk && !$d->file_attachment && !$d->file_kontrak)
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('members.show', $d->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-colors">
                                        <i class="fa fa-eye text-xs"></i> Detail
                                    </a>
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-nama="{{ $d->nama }}"
                                        data-kontak="{{ $d->kontak }}"
                                        data-email="{{ $d->email }}"
                                        data-alamat="{{ $d->alamat }}"
                                        data-jenis_member="{{ $d->jenis_member }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('members.destroy', $d->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus member ini? Kendaraan yang terhubung akan terlepas.')" class="inline">
                                        @csrf @method('DELETE')
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
                                        <i class="bi bi-people-fill text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data member</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Member" untuk menambahkan mitra baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
        <div class="px-5 pb-3 text-xs text-gray-400" id="entriesInfo"></div>
    </div>

</div>

{{-- ══ MODAL TAMBAH / EDIT ══ --}}
<div id="memberModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Member</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data mitra pemilik kendaraan</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="memberForm" action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-5">
            @csrf
            <div id="methodContainer"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="f_nama" required placeholder="Nama lengkap / perusahaan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak</label>
                    <input type="text" name="kontak" id="f_kontak" placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                    <input type="email" name="email" id="f_email" placeholder="contoh@email.com"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Member <span class="text-red-500">*</span></label>
                    <select name="jenis_member" id="f_jenis_member" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="perorangan">Perorangan</option>
                        <option value="perusahaan">Perusahaan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                    <textarea name="alamat" id="f_alamat" rows="2" placeholder="Alamat lengkap..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-3">
                    <i class="fa fa-folder-open text-blue-400 mr-1"></i> Dokumen
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">File STNK <span class="text-amber-400 font-normal">(bisa lebih dari 1)</span></label>
                        <div onclick="document.getElementById('f_stnk').click()"
                            class="border-2 border-dashed border-amber-300 bg-amber-50 rounded-xl p-4 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-amber-100 transition min-h-[90px]">
                            <input type="file" name="file_stnk[]" id="f_stnk" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                multiple onchange="previewMultipleFiles(this,'prev_stnk_list','nama_stnk_count','amber')">
                            <i class="fas fa-id-card text-amber-500 text-xl mb-1"></i>
                            <p class="text-xs text-amber-700 font-medium">Upload STNK</p>
                            <p id="nama_stnk_count" class="hidden mt-1 text-xs text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full max-w-full truncate"></p>
                        </div>
                        <div id="prev_stnk_list" class="hidden mt-2 space-y-1 max-h-32 overflow-y-auto"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Attachment <span class="text-purple-400 font-normal">(bisa lebih dari 1)</span></label>
                        <div onclick="document.getElementById('f_attachment').click()"
                            class="border-2 border-dashed border-purple-300 bg-purple-50 rounded-xl p-4 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-purple-100 transition min-h-[90px]">
                            <input type="file" name="file_attachment[]" id="f_attachment" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                multiple onchange="previewMultipleFiles(this,'prev_attachment_list','nama_attachment_count')">
                            <i class="fas fa-paperclip text-purple-500 text-xl mb-1"></i>
                            <p class="text-xs text-purple-700 font-medium">Upload Attachment</p>
                            <p id="nama_attachment_count" class="hidden mt-1 text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full max-w-full truncate"></p>
                        </div>
                        <div id="prev_attachment_list" class="hidden mt-2 space-y-1 max-h-32 overflow-y-auto"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Kontrak</label>
                        <div onclick="document.getElementById('f_kontrak').click()"
                            class="border-2 border-dashed border-teal-300 bg-teal-50 rounded-xl p-4 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-teal-100 transition min-h-[90px]">
                            <input type="file" name="file_kontrak" id="f_kontrak" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                onchange="previewFile(this,'prev_kontrak','nama_kontrak')">
                            <i class="fas fa-file-contract text-teal-500 text-xl mb-1"></i>
                            <p class="text-xs text-teal-700 font-medium">Upload Kontrak</p>
                            <p id="nama_kontrak" class="hidden mt-1 text-xs text-teal-600 bg-teal-100 px-2 py-0.5 rounded-full max-w-full truncate"></p>
                        </div>
                        <img id="prev_kontrak" class="hidden mt-2 w-full h-20 object-cover rounded-lg border border-teal-200" alt="">
                    </div>

                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    <i class="fa fa-save text-sm"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- POPUP ALERT --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1 min-w-0"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1 min-w-0"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                @if(session('error'))<p class="text-xs text-gray-500 mt-0.5">{{ session('error') }}</p>
                @else<ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none mt-0.5 flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
</style>

<script>
const memberModal = document.getElementById('memberModal');
const memberForm  = document.getElementById('memberForm');
const methodContainer = document.getElementById('methodContainer');

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Member';
    memberForm.action = '{{ route("members.store") }}';
    methodContainer.innerHTML = '';
    memberForm.reset();
    ['stnk','attachment'].forEach(k => {
        document.getElementById('nama_'+k+'_count').classList.add('hidden');
        document.getElementById('prev_'+k+'_list').classList.add('hidden');
        document.getElementById('prev_'+k+'_list').innerHTML = '';
    });
    document.getElementById('prev_kontrak').classList.add('hidden');
    document.getElementById('nama_kontrak').classList.add('hidden');
    memberModal.classList.remove('hidden');
    memberModal.classList.add('flex');
}
function closeModal() {
    memberModal.classList.add('hidden');
    memberModal.classList.remove('flex');
}
memberModal.addEventListener('click', e => { if(e.target===memberModal) closeModal(); });

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Member';
    memberForm.action = '/admin/members/' + btn.dataset.id;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_nama').value         = btn.dataset.nama;
    document.getElementById('f_kontak').value       = btn.dataset.kontak;
    document.getElementById('f_email').value        = btn.dataset.email;
    document.getElementById('f_jenis_member').value = btn.dataset.jenis_member;
    document.getElementById('f_alamat').value       = btn.dataset.alamat;
    ['stnk','attachment'].forEach(k => {
        document.getElementById('nama_'+k+'_count').classList.add('hidden');
        document.getElementById('prev_'+k+'_list').classList.add('hidden');
        document.getElementById('prev_'+k+'_list').innerHTML = '';
    });
    document.getElementById('prev_kontrak').classList.add('hidden');
    document.getElementById('nama_kontrak').classList.add('hidden');
    memberModal.classList.remove('hidden');
    memberModal.classList.add('flex');
}

function previewFile(input, previewId, namaId) {
    const file = input.files[0];
    if (!file) return;
    const nama = document.getElementById(namaId);
    nama.textContent = '✔ ' + file.name;
    nama.classList.remove('hidden');
    const prev = document.getElementById(previewId);
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    } else {
        prev.classList.add('hidden');
    }
}

function previewMultipleFiles(input, listId, countId, color) {
    color = color || 'purple';
    const files = Array.from(input.files);
    const countEl = document.getElementById(countId);
    const listEl  = document.getElementById(listId);

    if (!files.length) {
        countEl.classList.add('hidden');
        listEl.classList.add('hidden');
        listEl.innerHTML = '';
        return;
    }

    countEl.textContent = '✔ ' + files.length + ' file dipilih';
    countEl.classList.remove('hidden');

    listEl.innerHTML = '';
    listEl.classList.remove('hidden');

    files.forEach((file) => {
        const row = document.createElement('div');
        row.className = `flex items-center gap-2 bg-${color}-50 border border-${color}-200 rounded-lg px-2 py-1.5`;

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.className = 'w-8 h-8 object-cover rounded border border-gray-200 flex-shrink-0';
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; };
            reader.readAsDataURL(file);
            row.appendChild(img);
        } else {
            const iconEl = document.createElement('i');
            iconEl.className = `fas fa-file-pdf text-red-400 text-lg flex-shrink-0 w-8 text-center`;
            row.appendChild(iconEl);
        }

        const name = document.createElement('span');
        name.className = `text-xs text-${color}-700 truncate flex-1 min-w-0`;
        name.textContent = file.name;
        row.appendChild(name);

        const size = document.createElement('span');
        size.className = 'text-xs text-gray-400 flex-shrink-0';
        size.textContent = (file.size / 1024).toFixed(0) + ' KB';
        row.appendChild(size);

        listEl.appendChild(row);
    });
}

const allRows = Array.from(document.querySelectorAll('#memberTableBody tr[data-search]'));
let currentSearch = '';
function filterTable(q) {
    currentSearch = q.toLowerCase();
    document.getElementById('pdfBtn').href = '{{ route("members.pdf") }}?search=' + encodeURIComponent(q);
    renderTable();
}
function renderTable() {
    const perPage = document.getElementById('perPageSelect').value === 'all' ? Infinity : parseInt(document.getElementById('perPageSelect').value, 10);
    const matched = allRows.filter(r => r.dataset.search.includes(currentSearch));
    let shown = 0;
    allRows.forEach(r => r.style.display = 'none');
    matched.forEach(r => { if (shown < perPage) { r.style.display = ''; shown++; } });
    const info = matched.length === 0 ? 'Tidak ada data' : `Menampilkan ${shown} dari ${matched.length} entri` + (currentSearch ? ' (pencarian)' : '');
    ['entriesInfoTop','entriesInfo'].forEach(id => { const el = document.getElementById(id); if(el) el.innerText = info; });
}
document.addEventListener('DOMContentLoaded', renderTable);

(function() {
    const overlay = document.getElementById('alertOverlay');
    const box = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(() => { overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 80);
    const timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', e => { if(e.target===overlay) closeAlert(); });
    function closeAlert() { clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>
@endsection
